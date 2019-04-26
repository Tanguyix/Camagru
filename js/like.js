function like_pic(pic_id) {
    let formReq = new XMLHttpRequest();

    formReq.onreadystatechange = function() {
        if (formReq.readyState === 4) {
            if (formReq.status === 200) {
                let heart = document.getElementById("h" + pic_id);
                let nb = document.getElementById("l" + pic_id);
                if (nb == null) {return;}
                let act = parseInt(nb.innerHTML);
                if (formReq.responseText == "redirect") {
                    window.location.replace("login.php");
                }
                if (formReq.responseText == "added") { //put the heart in red, add 1 to the counter
                    heart.src = "img/heart.png";
                    nb.innerHTML = act + 1;
                }
                else if (formReq.responseText == "deleted") { //put the heart in black, remove 1 from the counter
                    heart.src = "img/black_heart.png";
                    nb.innerHTML = act - 1;
                }
            } else {
                // not OK
                alert('failure!');
            }
        }
    };

    //send form
    formReq.open("POST", "../utils/like.php", true);
    formReq.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    formReq.send('pic_id=' + pic_id);
}