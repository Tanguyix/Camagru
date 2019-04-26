function send_comment(id, username) {
    let formReq = new XMLHttpRequest();

    let commentboxes = document.getElementById("cb" + id);
    if (commentboxes == null) {return;}
    let comment = (commentboxes.value);
    if (comment.length == 0) {return;}

    formReq.onreadystatechange = function() {
        if (formReq.readyState === 4) {
            if (formReq.status === 200) {
                if (formReq.responseText == "redirect") {
                    window.location.replace("login.php");
                }
                let acts = document.getElementById("c" + id);
                acts.innerHTML += formReq.responseText;
                commentboxes.value = "";
            }
             else {
                // not OK
                alert('failure!');
            }
        }
    };
    formReq.open("POST", "../utils/comment.php", true);
    formReq.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    formReq.send('id=' + id + '&comment=' + comment + '&username=' + username);
}

function enter_com(e, id, username) {
   if (e.code == "Enter") {
       e.preventDefault();
       send_comment(id, username);
   }
}