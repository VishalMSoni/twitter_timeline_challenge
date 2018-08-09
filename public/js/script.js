// Search part
function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
          });
          a.appendChild(b);
        }
      }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener("click", function (e) {
      closeAllLists(e.target);
      });
}

// //Slider part
// var slide_index = 1;  
// displaySlides(slide_index);  
// function nextSlide(n) {  
//     displaySlides(slide_index += n);  
// }  
// function currentSlide(n) {  
//     displaySlides(slide_index = n);  
// }  
// function displaySlides(n) {  
//     var i;  
//     var slides = document.getElementsByClassName("showSlide");  
//     if (n > slides.length) 
//     { 
//       slide_index = 1 
//     }  
//     if (n < 1) 
//     { 
//       slide_index = slides.length 
//     }  
//     for (i = 0; i < slides.length; i++) {  
//         slides[i].style.display = "none";  
//     }  
//     slides[slide_index - 1].style.display = "block";  
// }  

function Slider( element ) {
  this.el = document.querySelector( element );
  this.init();
} 

Slider.prototype = {
  init: function() {
    this.links = this.el.querySelectorAll( "#slider-nav a" );
    this.wrapper = this.el.querySelector( "#slider-wrapper" );
    this.navigate();
  },
  navigate: function() {
  
    for( var i = 0; i < this.links.length; ++i ) {
      var link = this.links[i];
      this.slide( link ); 
    }
  },
  
  animate: function( slide ) {
    var parent = slide.parentNode;
    var caption = slide.querySelector( ".caption" );
    var captions = parent.querySelectorAll( ".caption" );
    for( var k = 0; k < captions.length; ++k ) {
      var cap = captions[k];
      if( cap !== caption ) {
        cap.classList.remove( "visible" );
      }
    }
    caption.classList.add( "visible" ); 
  },
  
  slide: function( element ) {
    var self = this;
    element.addEventListener( "click", function( e ) {
      e.preventDefault();
      var a = this;
      self.setCurrentLink( a );
      var index = parseInt( a.getAttribute( "data-slide" ), 10 ) + 1;
      var currentSlide = self.el.querySelector( ".slide:nth-child(" + index + ")" );
      
      self.wrapper.style.left = "-" + currentSlide.offsetLeft + "px";
      self.animate( currentSlide );
      
    }, false);
  },
  setCurrentLink: function( link ) {
    var parent = link.parentNode;
    var a = parent.querySelectorAll( "a" );
    
    link.className = "current";
    
    for( var j = 0; j < a.length; ++j ) {
      var cur = a[j];
      if( cur !== link ) {
        cur.className = "";
      }
    }
  } 
};

document.addEventListener( "DOMContentLoaded", function() {
  var aSlider = new Slider( "#slider" );  
});

// var source   = document.getElementById("entry-template").innerHTML;
// var template = Handlebars.compile(source);
// var context = {title: "My New Post", body: "This is my first post!"};
// var theCompiledHtml    = template(context);
// $('.content-placeholder').html(theCompiledHtml);