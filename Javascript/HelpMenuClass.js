class HelpMenuClass{
    
     constructor(htmlURL){
         
        this.showBoolean = true; // set to true for the first click so it will be shown
         
        // html for the elments
        this.helpElementsHTML = "<div id='help-screen-overlay'></div> <div id='help-content-container'><button id='help-close-button'>X</button><div id='help-content-html'></div></div></div><button id='help-click-button'>?</button>";
         
        // css for elements
         this.overlayCSS = {"display": "none", "position": "fixed", "top": "0px", "left": "0px", "height": "100vh", "width": "100vw", "background-color": "gray", "opacity": "0.7", "z-index": "2147483645"};
		 
         this.helpContainerCSS = {'position': 'fixed', 'overflow': 'auto', 'display': 'none', 'top': '50%', 'left': '50%', 'transform': 'translate(-50%, -50%)', 'z-index': '2147483646', 'width': '700px', 'height': 'auto', 'background-color': 'white', 'padding': '30px', 'border-radius': '2vh', 'box-shadow': '3px 3px 3px #878787'};
         
         this.closButtonCSS = {      'position': 'absolute',      'right': '15',      'top': '5',      'color': 'black',      'background-color': 'transparent',      'border-style': 'none',      'font-size': '3vh',      'font-weight': '800'  };
         
         this.helpButtonCSS = { 'width': '6vh', 'height': '5vh', 'background-color': 'orange','color': 'white', 'font-weight': '600', 'position': 'fixed', 'bottom': '20', 'right': '20', 'z-index': '2147483647', 'font-size': '4vh', 'text-align': 'center', 'border-radius': '1vh', 'border-style': 'hidden'};
         
         this.contentContainerCSS = {'overflow': 'auto'};
         
        let _this = this; // used to pass in reference to this object for events
        this.htmlURL = htmlURL; // html content that goes inside the help container
        this.onPageLoad(_this); // peform actions that require page to be loaded
    }
    
    
    onPageLoad(_this){
        $('document').ready(function(){
            
             // add help class html to page
             $('body').append(_this.helpElementsHTML);
            
            // get the html based on provided url and insert into help container
            $('#help-content-html').load(_this.htmlURL);


            // set html
            $('#help-screen-overlay').css(_this.overlayCSS);
            $('#help-content-container').css(_this.helpContainerCSS);
            $('#help-close-button').css(_this.closButtonCSS);
            $('#help-content-html').css(_this.contentContainerCSS);
            $('#help-click-button').css(_this.helpButtonCSS);
            
            
            // add event listeners when document is fully loaded
            $('#help-click-button').click(function(){_this.showHideHelp(_this)});
            $('#help-close-button').click(function(){_this.showHideHelp(_this)});
            
        });
    }
    
    showHideHelp(_this){
        
        if (_this.showBoolean == true){
            // 
            $('#help-screen-overlay').fadeIn(1000);
            $('#help-content-container').fadeIn(1500);
            
            _this.showBoolean = false;
        }
        else{
            
            $('#help-screen-overlay').fadeOut(2000);
            $('#help-content-container').fadeOut(1000);
            
            _this.showBoolean = true;
        }
            
    }
}

export { HelpMenuClass };