string  PrimKey;
string  Url= "URL TO your server php file";
float   UpdateTime= 20; //prim updates in Sec

string  DefaultKey= "00000000-0000-0000-0000-0000";
list    PrimParmLst;
list    PrevPrimParmLst;
key     HttpRequestId;
string  NoteName= "CloneKey";
integer Line= 0;
key     NoteQueryId; 
string  OwnerName;


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

ReadDefaultKey() {
    string ParsStr= "key="+DefaultKey+"&func="+"GET";
    HttpRequestId= llHTTPRequest(Url,[HTTP_METHOD, "POST",HTTP_MIMETYPE,"application/x-www-form-urlencoded"],ParsStr);
}    

default
{
    state_entry() {
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
            string ParsStr= "key="+OwnerName+"-"+PrimKey+"&func="+"GET";
            HttpRequestId= llHTTPRequest(Url,[HTTP_METHOD, "POST",HTTP_MIMETYPE,"application/x-www-form-urlencoded"],ParsStr); 
        }    
    }        
     
    http_response(key RequestId, integer Status, list MetaData, string Body) {
        if (RequestId == HttpRequestId) {
            if (Status == 200) {
                string PrimData= llStringTrim(GetSubString(Body,"&data=" ,"EOL"),STRING_TRIM);
                string Hash=     llStringTrim(GetSubString(Body,"&crypt=","&data="),STRING_TRIM); 
                //llOwnerSay(Hash+"  "+llMD5String(PrimData,0)); 
                if (Hash == llMD5String(PrimData,0)) {            
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
        
