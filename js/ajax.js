formButton = document.querySelector(".upload_pic");

formButton.onclick = () => {
    let formReq = new XMLHttpRequest();
    let legend = document.getElementById("legend").value.trim().substring(0, 60);
    let lbox =  document.getElementById("legend");

    if (legend == "") {lbox.style.border = "2px solid red";  return;} else {lbox.style.border = "1px solid black";}
    //check if form works
    formReq.onreadystatechange = function() {
        if (formReq.readyState === 4) {
            if (formReq.status === 200) {
                if (formReq.responseText == "img_problem") {
                    window.location.replace("../montage.php?error=img_pb");
                    return;
                }
                let pastpics = document.getElementById("pastpics");
                let save = pastpics.innerHTML;
                pastpics.innerHTML = formReq.responseText + save;
                let legendbox = document.getElementById("legend");
                legendbox.value = "";
                let taken = document.getElementById("taken");
                if (taken == null) {
                    window.location.replace("../montage.php");
                    return;
                }
                let sticker = document.getElementsByClassName("sticked")[0];
                sticker.removeAttribute("src");
                retake_pic();
                let button_take = document.getElementsByClassName("test")[0];
                button_take.disabled = true;
                let button_up = document.getElementsByClassName("upload_pic")[0];
                button_up.disabled = true;
            } else {
                // not OK
                alert('failure!');
            }
        }
    };
    let pic;
    //get src for sticker and pic
    if(document.getElementById("uploaded") !== null){
        pic = document.getElementById("uploaded").src;
    }else if (document.getElementById("taken") !== null) {
        pic = document.getElementById("taken").src;
    }
    if (pic == 0) {
        window.location.replace("../montage.php?error=no_photo");
        return;
    }
    let stick = document.getElementsByClassName("sticked");
    if (stick.length == 0 || stick[0] == null || stick[0].src == 0) {
        window.location.replace("../montage.php?error=no_sticker");
        return;
    }
    let style = getComputedStyle(stick[0]);
    let stick_png = stick[0].src;
    let stick_top = style.top;
    let stick_left = style.left;
    let stick_width = style.width;
    if (legend.length == 0) { window.location.replace("../montage.php?error=nolegend");
    return;
    }
    //send form
        formReq.open("POST", "../utils/stick.php", true);
        formReq.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        formReq.send('pic=' + pic + '&sticker=' + stick_png + '&stick_top=' + stick_top + '&stick_left=' + stick_left + '&stick_width=' + stick_width + '&legend=' + legend);
}

function erase_pic(id) {
    let formReq = new XMLHttpRequest();

    formReq.onreadystatechange = function() {
        if (formReq.readyState === 4) {
            if (formReq.status === 200) {
                let elem = document.getElementById(id);
                elem.parentNode.removeChild(elem);
            } else {
                // not OK
                alert('failure!');
            }
        }
    };

    if (confirm("Are you sure you want to delete this picture?")) {
        //send form
        formReq.open("POST", "../utils/delete.php", true);
        formReq.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        formReq.send('id=' + id);
    }
}

