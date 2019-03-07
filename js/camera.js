let video = document.querySelector("#videoElement");
 
if (navigator.mediaDevices.getUserMedia) {       
    navigator.mediaDevices.getUserMedia({video: true})
  .then(function(stream) {
    video.srcObject = stream;
  })
  .catch(function(err0r) {
    console.log("Something went wrong!");
  });
}

function take_pic() {
    let canvas = document.querySelector('.mycanva'),
        video = document.querySelector('#videoElement'),
        image = document.querySelector('#taken'),

    // Get the exact size of the video element.
        width = video.videoWidth,
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
}

let stick = document.getElementsByClassName('stick');
for (let i=0; i < stick.length; i++)
{
    stick[i].addEventListener('click', function() {
        let sticker = document.querySelector('.sticked')

        sticker.setAttribute('src', this.src);
    });
}

let top_stick = 45;
let left_stick = 200;

function mv_sticker(id) {
    let sticker = document.getElementsByClassName("sticked");

    if (id == "up" && top_stick >= 10)
        top_stick -= 10;
    else if (id == "down" && top_stick < 300)
        top_stick += 10;
    else if (id == "left" && left_stick >= 10)
        left_stick -= 10;
    else if (id == "right" && left_stick <= 380)
        left_stick += 10;
    sticker[0].style.top = top_stick + "px";
    sticker[0].style.left = left_stick + "px";
}


function size_sticker(id) {
    let sticker = document.getElementsByClassName("sticked");
    let width = sticker[0].offsetWidth;

    if (id == "plus" && width <= 700)
        width += 30;
    else if (id == "minus" && width >= 20)
        width -= 30;
    sticker[0].style.width = width + 'px';
}