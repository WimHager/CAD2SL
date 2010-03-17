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

string  Url;
float   UpdateTime= 2; //prim updates in Sec
integer CommCh= -2010;

string  ObjectName;
integer PrimNr;
integer ObjectPrims= 0;
string  PrimKey;
integer PrimRezzed;

list    PrimParmLst;
list    PrevPrimParmLst;
key     HttpRequestId;
integer ListenHandle;
vector  OrgPos;
integer Debug;


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
    string NewPosStr= "1=6|5="+(string)NewPos+"|";
    string NewStream= StrReplace(Stream,PosStr,NewPosStr);
    //llOwnerSay(NewStream);
    return NewStream;
}    

default
{

    timer() {
        string ParsStr= "&name="+ObjectName+"&primnr="+(string)PrimNr;
        HttpRequestId= llHTTPRequest(Url,[HTTP_METHOD, "POST",HTTP_MIMETYPE,"application/x-www-form-urlencoded"],ParsStr); 
    }        
     
    http_response(key RequestId, integer Status, list MetaData, string Body) {
        if (RequestId == HttpRequestId) {
            if (Status == 200) {
                string PrimData=      llStringTrim(GetSubString(Body,"&data=" ,"&total"),STRING_TRIM);
                string Hash=          llStringTrim(GetSubString(Body,"&md5=","&data="),STRING_TRIM);
                ObjectPrims= (integer)llStringTrim(GetSubString(Body,"&total=","EOL"),STRING_TRIM);
                //llOwnerSay(PrimData);
                if (Hash == llMD5String(PrimData,0)) { 
                    PrimData= PosCorrection(PrimData); //Add Pos to rezz point           
                    if (Debug) llOwnerSay("URL: "+Url+" "+ObjectName); 
                    if (Debug) llOwnerSay(PrimData);
                    PrimParmLst= Deserialize(PrimData);
                    if (!ListCompare(PrimParmLst,PrevPrimParmLst)) {
                        //llOwnerSay("Master Prim Data Changed.");
                        llSetPrimitiveParams(PrimParmLst); 
                        PrevPrimParmLst= PrimParmLst;
                        llSetText("",<0,0,0>,0);
                        llSetTimerEvent(0); //Stop updating
                    }
                    }else{ llOwnerSay("Received Data is corrupted!!! "+Hash+" "+llMD5String(PrimData,0)); }    
            }else{ llSetText("Server error: "+(string)Status,<1,1,1>,1); }  
        } 
    }
    
    listen( integer Ch, string Name, key ID, string MsgStr) {
        if ( Ch == CommCh ) {
        list MsgL= llParseString2List(MsgStr, ["|"], []);
            if ( llList2String(MsgL,0) == "Kill" ) { llSay(0,"Outch prim "+(string)PrimNr+" is killed"); llDie(); }
            Url= llList2String(MsgL,0);
            ObjectName= llList2String(MsgL,1); 
            Debug= llList2Integer(MsgL,2);
            if (!PrimRezzed) llSetTimerEvent(UpdateTime); 
            PrimRezzed= TRUE;
        }    
    }   
    
    on_rez(integer StartParm) {
        PrimNr= StartParm;
        Url= "";
        Debug= FALSE;
        OrgPos= llGetPos();
        PrimRezzed= FALSE;  //Ugly need a redo !!!
        llSetPrimitiveParams([PRIM_SIZE, <0.1,0.1,0.1>]);
        llSetText("Initialize...",<1,1,1>,1);
        ListenHandle= llListen(CommCh,"",NULL_KEY, "");
    }                                            

}        
        
