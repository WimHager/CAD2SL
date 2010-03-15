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

//ToDo first get prim count.
string  Url= "http://to your/cad2sl-bridge.php";
string  ObjectName;
integer ObjectPrims;
integer CommCh= -2010;
key     HttpRequestId;
integer Counter;

string GetSubString(string String, string StartStr, string EndStr) { // Use EOL for End Of line
    integer Start= llSubStringIndex(String, StartStr)+llStringLength(StartStr);
    integer End=   llSubStringIndex(String, EndStr)-1;
    if (EndStr=="EOL") End= -1;
    return llGetSubString(String,Start,End);    
}   

default
{
    state_entry() {
        llSay(CommCh,"Kill"); // Lets Kill them first
    }
     
    touch_start(integer NDetect) {
        llSay(CommCh,"Kill");
        ObjectName= llGetObjectDesc();
        Counter= 0;
        string ParsStr= "&name="+ObjectName+"&primnr=0"; //Get Object Prims
        HttpRequestId= llHTTPRequest(Url,[HTTP_METHOD, "POST",HTTP_MIMETYPE,"application/x-www-form-urlencoded"],ParsStr);         
        llSetTimerEvent(2);
    }

    timer() {
        llRezObject("builder", llGetPos() + <0.0,0.0,0.5>, <0.0,0.0,0.0>, <0.0,0.0,0.0,0.0>, Counter);
        llSleep(0.3); //Wait for Rezz        
        llSay(CommCh,ObjectName);        
        Counter++;
        if (Counter >= ObjectPrims) llSetTimerEvent(0);
    }
     
    http_response(key RequestId, integer Status, list MetaData, string Body) {
        if (RequestId == HttpRequestId) {
            if (Status == 200) {
                ObjectPrims= (integer)llStringTrim(GetSubString(Body,"&total=","EOL"),STRING_TRIM);
                llSay(0,"Object has: "+(string)ObjectPrims+" prims");
            }    
        } 
    }
    
}
