let video = document.querySelector("#videoElement");
 
if (navigator.mediaDevices.getUserMedia) {       
    navigator.mediaDevices.getUserMedia({video: true})
  .then(function(stream) {
    video.srcObject = stream;
  })
  .catch(function(err0r) {
  });
}

function retake_pic() {
    let image = document.querySelector('#taken');
    let camera = document.getElementsByClassName("camera")[0];
    let video = document.querySelector("#videoElement");

    if (video != null) {return;}
    camera.removeChild(image);
    camera.innerHTML += "<video autoplay=\"true\" id=\"videoElement\"></video>";
    camera.innerHTML += "<canvas class=\"mycanva\"></canvas>";
    camera.innerHTML += "<img id=\"taken\">";
    video = document.querySelector("#videoElement");
    if (navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({video: true})
            .then(function (stream) {
                video.srcObject = stream;
            })
            .catch(function (err0r) {
                console.log("Something went wrong!");
            });
    }
    let take = document.getElementsByClassName("test");
    take[0].disabled = false;
}

function take_pic() {
    let canvas = document.querySelector('.mycanva'),
        video = document.querySelector('#videoElement'),
        image = document.querySelector('#taken');

    if (canvas == null || video == null || image == null || video.srcObject == null)
        return;

    // Get the exact size of the video element.
     let   width = video.videoWidth,
        height = video.videoHeight,

        // Context object for working with the canvas.
         context = canvas.getContext('2d');

    //Set the canvas to the same dimensions as the video.
    canvas.width = width;
    canvas.height = height;

    // Draw a copy of the current frame from the video on the canvas.
    context.drawImage(video, 0, 0, width, height);

    // Get an image dataURL from the canvas.
    let imageDataURL = canvas.toDataURL('image/png');

    // Set the dataURL as source of an image element, showing the captured photo.
    image.setAttribute('src', imageDataURL);
    canvas.parentNode.removeChild(canvas);
    video.parentNode.removeChild(video);
    let button = document.getElementsByClassName("upload_pic");
    button[0].disabled = false;
    let take = document.getElementsByClassName("test");
    take[0].disabled = true;
    let reload = document.getElementsByClassName("reload")[0];
    reload.disabled = false;
}

let stick = document.getElementsByClassName('stick');
for (let i=0; i < stick.length; i++)
{
    stick[i].addEventListener('click', function() {
        let sticker = document.querySelector('.sticked');
        let button = document.getElementsByClassName("test");
        if (button[0]) {
            button[0].disabled = false;
        }
        sticker.setAttribute('src', this.src);
        dragElement(sticker);
    });
}

function size_sticker(id) {
    let sticker = document.getElementsByClassName("sticked");
    let width = sticker[0].offsetWidth;

    if (id == "plus")
        width += 30;
    else if (id == "minus")
        width -= 30;
    sticker[0].style.width = width + 'px';
}

let sticker = document.getElementsByClassName("sticked")[0];

function reset_pos() {
    sticker.style.left = 200 + 'px';
    sticker.style.top = 45 + 'px';
    sticker.style.width = 300 + 'px';
}

// Make the DIV element draggable:
dragElement(sticker);

function dragElement(elmnt) {
    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    if (document.getElementById(elmnt.id + "header")) {
        // if present, the header is where you move the DIV from:
        document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
    } else {
        // otherwise, move the DIV from anywhere inside the DIV:
        elmnt.onmousedown = dragMouseDown;
    }

    function dragMouseDown(e) {
        e = e || window.event;
        e.preventDefault();
        // get the mouse cursor position at startup:
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        // call a function whenever the cursor moves:
        document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
        e = e || window.event;
        e.preventDefault();
        // calculate the new cursor position:
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        // set the element's new position:
        elmnt.style.top = (((elmnt.offsetTop - pos2) > - 200 && ((elmnt.offsetTop - pos2) < 400 )) ? (elmnt.offsetTop - pos2) : ((elmnt.offsetTop - pos2) > 0 ? 400 : -200)) + "px";
        elmnt.style.left = (((elmnt.offsetLeft - pos1) > - 200 && ((elmnt.offsetLeft - pos1) < 550 )) ? (elmnt.offsetLeft - pos1) : ((elmnt.offsetLeft - pos1) > 0 ? 550 : -200)) + "px";
    }

    function closeDragElement() {
        // stop moving when mouse button is released:
        document.onmouseup = null;
        document.onmousemove = null;
    }
}