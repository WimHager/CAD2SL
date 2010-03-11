//    This file is part of CAD2SL.
//
//    CAD2SL is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    CAD2SL is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with CAD2SL.  If not, see <http://www.gnu.org/licenses/>.

//ToDo test with longer stream data


string  PrimKey;
string  Url= "Path to cad2sl-bridge.php";
float   UpdateTime= 20; //prim updates in Sec

string  DefaultKey= "00000000-0000-0000-0000-0000";
list    PrimParmLst;
list    PrevPrimParmLst;
key     HttpRequestId;
string  NoteName= "ObjectName";
integer Line= 0;
key     NoteQueryId; 
string  OwnerName;
vector  OrgPos;


string GetSubString(string String, string StartStr, string EndStr) { // Use EOL for End Of line
    integer Start= llSubStringIndex(String, StartStr)+llStringLength(StartStr);
    integer End=   llSubStringIndex(String, EndStr)-1;
    if (EndStr=="EOL") End= -1;
    return llGetSubString(String,Start,End);    
}   

integer ListCompare(list A, list B) {
    integer AL = A != [];
    if (AL != (B != [])) return 0;
    if (AL == 0)         return 1;
    return !llListFindList((A = []) + A, (B = []) + B);
}

list Deserialize(string Inp){
    integer Len= ~(integer)Inp; // "|" take care
    list    Build= [];
    list    Pair= llParseString2List(Inp, ["|"], []);
    string  Value;
    list    Replace;
    Pair= llList2List(Pair,1,-1); //Remove lenght from list
    while(++Len){
        integer Type= (integer)(Value = llList2String(Pair, Len));
        Value= llDeleteSubString(Value, 0, llSubStringIndex(Value, "="));
        if(TYPE_INTEGER == Type)        Replace= [(integer)Value];
        else if(TYPE_FLOAT == Type)     Replace= [(float)Value];
        else if(TYPE_STRING == Type)    Replace= [Value];
        else if(TYPE_KEY == Type)       Replace= [(key)Value];
        else if(TYPE_VECTOR == Type)    Replace= [(vector)Value];
        else if(TYPE_ROTATION == Type)  Replace= [(rotation)Value];
        Pair= llListReplaceList(Pair,   Replace, Len, Len);
    }
    return Pair;
}


string StrReplace(string Src, string From, string To) {//replaces all occurrences of 'from' with 'to' in 'src'.
    integer Len= (~-(llStringLength(From)));
    if(~Len) {
        string  Buffer= Src;
        integer BPos= -1;
        integer ToLen= (~-(llStringLength(To)));
        @Loop; //instead of a while loop, saves 5 bytes (and run faster).
        integer ToPos = ~llSubStringIndex(Buffer, From);
        if(ToPos) {
            Buffer= llGetSubString(Src= llInsertString(llDeleteSubString(Src, BPos -= ToPos, BPos + Len), BPos, To), (-~(BPos += ToLen)), 0x8000);
            jump Loop;
        }
    }
    return Src;
}

string GetPosPartFromStream(string Stream) {
    integer StartPos= llSubStringIndex(Stream,"|1=6|5=");       //Look for Pos start in stream
    string  TmpStr=   llDeleteSubString(Stream, 0, StartPos);
    integer EndPos=   llSubStringIndex(TmpStr,">")+1;
    return llGetSubString(TmpStr,0,EndPos);
}    

string PosCorrection(string Stream) { //Replaces Stream with Pos relative rezz point
    string PosStr= GetPosPartFromStream(Stream);
    vector CadPos= (vector)llGetSubString(PosStr,6,-1);
    vector NewPos= OrgPos+ CadPos; //add this to rezz Pos
    string NewPosStr= "1=6|5="+(string)NewPos;
    string NewStream= StrReplace(Stream,PosStr,NewPosStr);
    //llOwnerSay(NewStream);
    return NewStream;
}    

ReadDefaultKey() {
    string ParsStr= "key="+DefaultKey+"&func="+"GET";
    HttpRequestId= llHTTPRequest(Url,[HTTP_METHOD, "POST",HTTP_MIMETYPE,"application/x-www-form-urlencoded"],ParsStr);
}    

default
{
    state_entry() {
        OrgPos= llGetPos();
        llSetText("Initialize...",<1,1,1>,1);
        OwnerName= llKey2Name(llGetOwner());
        while (llGetInventoryType(NoteName) == -1) {
            llOwnerSay("Missing note CloneKey in contents of prim.");
            llSleep(20);
        }
        NoteQueryId = llGetNotecardLine(NoteName, Line);
        ReadDefaultKey();
        llSensorRepeat("", "", AGENT, 30.0, PI, UpdateTime);
    }

    sensor(integer Ndetect) {
        while (llGetInventoryType(NoteName) == -1) {
            llOwnerSay("Missing note CloneKey in contents of prim.");
            llSleep(20);
        }
        if (PrimKey == DefaultKey) {
            ReadDefaultKey();       
        }else{   
            string ParsStr= "&name=4wall.prim"+"&primnr="+"0";
            HttpRequestId= llHTTPRequest(Url,[HTTP_METHOD, "POST",HTTP_MIMETYPE,"application/x-www-form-urlencoded"],ParsStr); 
        }    
    }        
     
    http_response(key RequestId, integer Status, list MetaData, string Body) {
        if (RequestId == HttpRequestId) {
            if (Status == 200) {
                string PrimData= llStringTrim(GetSubString(Body,"&data=" ,"&total"),STRING_TRIM);
                string Hash=     llStringTrim(GetSubString(Body,"&md5=","&data="),STRING_TRIM);
                llOwnerSay(PrimData);
                if (Hash == llMD5String(PrimData,0)) { 
                    PrimData= PosCorrection(PrimData); //Add Pos to rezz point           
                    PrimParmLst= Deserialize(PrimData);
                    if (!ListCompare(PrimParmLst,PrevPrimParmLst)) {
                        llOwnerSay("Master Prim Data Changed.");
                        llSetPrimitiveParams(PrimParmLst); 
                        PrevPrimParmLst= PrimParmLst;
                        llSetText("",<0,0,0>,0);
                    }
                    }else{ llOwnerSay("Received Data is corrupted!!! "+Hash+" "+llMD5String(PrimData,0)); }    
            }else{ llSetText("Server error: "+(string)Status,<1,1,1>,1); }  
        } 
    }
    
   dataserver(key QueryId, string Data) {
        if (QueryId == NoteQueryId) {
            if (Data != EOF) { 
                PrimKey= llStringTrim(Data,STRING_TRIM);
                if (PrimKey==DefaultKey) {
                    llOwnerSay("Please, Edit note CloneKey with Master KEY.");
                    return;                                    
                }    
                llOwnerSay("Using Key: "+Data);
                llSetObjectDesc(PrimKey);
                ++Line;
                NoteQueryId= llGetNotecardLine(NoteName, Line);
            }
        }
    }
    
    changed(integer Change) {
        if ( Change & CHANGED_INVENTORY) {
            llOwnerSay("Reading CloneKey");
            if (llGetInventoryType("CloneKey") ==  -1) {
                llOwnerSay("Missing note CloneKey in contents of prim.");
            }else{
                NoteQueryId = llGetNotecardLine(NoteName, Line);
            }    
        }   
    }    

}        
        
