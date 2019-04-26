let start = 5;
window.onscroll = function () {
    if ((window.innerHeight + window.scrollY) >= document.body.scrollHeight) {
        infinite_display(start);
        start += 5;
    }
}


function infinite_display(start) {
    let formReq = new XMLHttpRequest();

    let gallery = document.getElementsByClassName("gallery")[0];
    formReq.onreadystatechange = function() {
        if (formReq.readyState === 4) {
            if (formReq.status === 200) {
                let arr = JSON.parse(formReq.responseText);
                for (let i in arr) {
                    let toadd ="";
                        toadd += "<div class='post'>" +
                        "<a href='profile.php?uid=" + arr[i]['user_id'] + "' class='creator'><img class='prof_pic' src='" + arr[i]['profile_pic'] + "'>" + arr[i]['name'] +"</a>" +
                        "<div class='centpic'><img class='photos' src='pictures/" + arr[i]['image'] + "'></div>" +
                        "<p id='l" + arr[i]['pid'] +  "' class='nb_likes'>" + arr[i]['likes'] + "</p>" +
                        "<img id='h" + arr[i]['pid'] + "' class='heart' onclick='like_pic(" + arr[i]['pid'] +")' src='" + (arr[i]['liked'] ? "img/heart.png" : "img/black_heart.png") +"'>" +
                        "<div class='comment'>" +
                            "<div class='legend'>" + arr[i]['legend'].replace(/</g, "&lt;").replace(/>/g, "&gt;") + "</div>" +
                            "<textarea onkeypress='enter_com(event, " + arr[i]['pid'] + ", \"" + arr[i]['lou'] + "\")' class='combox' placeholder='Add a comment...' id='cb" + arr[i]['pid'] + "' name='comment' rows='2' cols='60'></textarea>" +
                            "<button onclick='send_comment(" + arr[i]['pid'] + ", \"" + arr[i]['lou'] + "\")' class='commentajax'>Comment</button>" +
                            "<div class='actual_comments' id='c"+ arr[i]['pid'] + "'>";
                    for (let j = 0; j < arr[i]['coms'].length; j++) {
                        toadd += "<div id='cont'><a href='profile.php?uid=" + arr[i]['user_id'] + "' id='commentator'>" + arr[i]['coms'][j]['name'] + " : </a><p class='com'>" + arr[i]['coms'][j]['comment'].replace(/</g, "&lt;").replace(/>/g, "&gt;") + "</p></div>";
                    }
                        toadd += "</div></div>";
                    gallery.innerHTML += toadd;
                }
            } else {
                // not OK
                alert('failure!');
            }
        }
    };

    //send form
    formReq.open("POST", "../utils/display_pics.php", true);
    formReq.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    formReq.send('start=' + start);
}


    // <div id="cont"><p id="commentator"> <?php echo $com['name'] . " : "; ?></p><p class="com"> <?php echo $com['comment'] ;?></p></div>
