<!--
// <script language="JavaScript" src="/openforum.js"></script>

function ParseSubCookie (last_str, forumid) {
     var arg = forumid + "-";
     var alen = arg.length;
     var clen = last_str.length;
     var endstr = 0;
     var i = last_str.indexOf ("last=") + 5;

     while (i < clen) {
        var j = i + alen;
        if (last_str.substring(i, j) == arg){
             endstr = last_str.indexOf ("|", j);
             if (endstr == -1){
                  endstr = last_str.length;
             }
             return last_str.substring(j, endstr);
        }
        i = last_str.indexOf("|", i)+1;
        if (i == 0) { break; }
      }
      return null;
}

var last_visit_time = 0;

function s_n(msg_time, forumid){
    if (last_visit_time == 0){
	last_visit_time = ParseSubCookie(unescape(document.cookie), forumid);
    }
    if (last_visit_time == null){
	return;
    }
    if (msg_time > last_visit_time){
	document.write('<img src="/openforum/Images/newmark.gif" alt="!*!">');
    }
}
// -->
