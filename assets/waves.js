(function() {
    var loopID = undefined;
    var start = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || function(callback) {return window.setTimeout(callback, 1000 / 60);}
    var stop  = window.cancelAnimationFrame  || window.webkitCancelAnimationFrame  || window.mozCancelAnimationFrame  || window.oCancelAnimationFrame  || function(id) {window.clearTimeout(id);}
    window.loop = function(callback) {
        if(loopID != undefined) {
            stop(loopID);
            loopID = undefined;
        }
        if(callback) {
            loopID = start(function() {
                if(callback() !== false) loop(callback);
            });
        }
    };
})();
var width = window.innerWidth;
var height = 400;
var frames = 0;
var canvas = document.getElementById("animation");
canvas.width = width;
canvas.height = height;
var ctx = canvas.getContext("2d");
var randomInt = function(low, high) {return low + Math.floor((high - low) * Math.random());};
var Bubbles = function(x, y) {
    this.x = x;
    this.y = y;
    this.r = randomInt(10, 40);
    this.display = function(ctx) {
        ctx.beginPath();
        ctx.strokeStyle = "#55f";
        ctx.arc(this.x, this.y, this.r, 0, 2 * Math.PI, false);
        ctx.closePath();
        ctx.stroke();
    };
};

var circle = function(x, y, color) {
    ctx.fillStyle = color;
    ctx.beginPath();
    ctx.arc(x, y, 4, 0, 2 * Math.PI, false);
    ctx.closePath();
    ctx.fill();
};

var wave = function(wavelength, xPosition, period, time) {
    return Math.sin( 2*Math.PI*(xPosition/wavelength - time/period) );
};

/* Draw loop */
window.loop(function() {
    ctx.clearRect(0, 0, width, height);
    for(var x = 5; x < width - 5; x += 2) {
        var a = wave(30, x/2, 120, frames);
        var b = wave(25, x/2, 120, frames);
        var c = wave(35, x/2, 120, frames);
        var d = wave(35, x/2, 90, frames);
        circle(x, 5*height/8 + 20*a, "#778B94");
        circle(x, 5*height/8 + 20*b, "#d6856f");
        circle(x, 5*height/8 + 20*(a+b) + 60, "#94778B");
    }
    frames++;
});