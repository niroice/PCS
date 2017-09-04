    
$(document).ready(function(){
    
// change details will be stored here
var $claimChangeArray = new Array();
    
     // shows message box, with message
    function showMessage(type, message)
    {
        $('.warning-screen-cover').fadeIn(500);
        $('.warning-heading-type').html(type);
        $('.warning-heading-message').html(message);
        $('.warning-confirm-container').fadeIn(1000);
    }
    
    // hides the message box
    function hideMessage()
    {
        $('.warning-screen-cover').fadeOut(500);
        $('.warning-confirm-container').fadeOut(500);
    }
    
    $(document).on('click', '.warning-confirm-button', function(){
            hideMessage();
    });
    
    
    // if search button is clicked, run determainSearchType()
    $("#searchby-button-viewclaims").click(function(){
        determainSearchType();
    });
        
    // determains where to search by claimID or dropdown
    // if claimid input is not blank searcj by it over dropdown
    function determainSearchType()
    {
        // storing values from drop down box
        $viewby = $('#viewby-dropdown-viewclaims').val();
        $sortby = $('#sortby-dropdown-viewclaims').val();
        
        // store search input value into variable
        $claimID = $('#claimidsearch-input-viewclaims').val();
        
        // if claim ID is null search by dropdown
        if ($claimID == null || $claimID == "")
        {
            $.post("Classes/GenerateClaimsList.php",
            {
              ViewClaimBy : $viewby,
              SortClaimBy : $sortby
            },
            function(data,status){
                $('#claimlist-wrapper').html(data); // load data into claimlist-wrapper
            });
        }
        else // if no claim ID provided search by dropdown inputs
        {
            $.post("Classes/GenerateClaimsList.php",
            {
              claimID : $claimID
            },
            function(data,status){
                $('#claimlist-wrapper').html(data); // load data into claimlist-wrapper
            });
            
            // clear claimID field once complete
            $("#claimidsearch-input-viewclaims").val("");
        }
    }
    
    //select-10000206 .claim-status-select
    //$(".claim-status-select").change(function(){
    $(document).on('change', '.claim-status-select', function(){
        
        var claimID;
        var claimStatus;
        var claimType;
        
        // get claim id from the select - contains claimID number
        claimID = $(this).attr("id");

        // store the selected types value
        claimStatus = $(this).val();
        
        // store claim type
        claimType = $(this).attr("data-claimtype");
        
        //remove the "select-" from claimID string
        claimID = claimID.replace("select-","");
        
        //store current select into array to post later
        $claimChangeArray.push([ claimID, claimStatus, claimType ]);

    });
    

    // if save changes button is clicked post the details
    $(document).on('click', '#savechanges-button-claimsview', function(){
        $.post("Classes/ProcessClaimStatus.php",
        {
            StatusArray: $claimChangeArray
        },
         function(data,status){
            // reset the array after submitting
            $claimChangeArray = new Array();
            
            // make ajax call to update the notfication panel to reflect
            // the status changes, by inserting html
            $.post('UpdateStatusNotification.php', function(data,status){
                $('#notification-wrapper').html(data);
                showMessage("Success", "Claim/s Status Updated.");
                determainSearchType();
            });
            
        });
    });
    
    $(document).on('click', '.warning-confirm-button', function(){
        hideMessage();
    }); 
    
});

class HelpMenuClass{
    
     constructor(htmlURL){
         
        this.showBoolean = true; // set to true for the first click so it will be shown
         
        // html for the elments
        this.helpElementsHTML = "<div id='help-screen-overlay'></div> <div id='help-content-container'><button id='help-close-button'>X</button><div id='help-content-html'></div></div></div><button id='help-click-button'>?</button>";
         
        // css for elements
         this.overlayCSS = {"display": "none", "position": "fixed", "top": "0px", "left": "0px", "height": "100%", "width": "100%", "background-color": "rgba(0, 0, 0, 0.5)", "z-index": "2147483645"};
		 
         this.helpContainerCSS = {'position': 'fixed', 'overflow': 'auto', 'display': 'none', 'top': '50%', 'left': '50%', 'transform': 'translate(-50%, -50%)', 'z-index': '2147483646', 'width': '500px', 'height': 'auto', 'background-color': 'white', 'padding-top': '30px','padding-bottom': '40px', 'padding-left': '40px', 'padding-right': '40px', 'border-radius': '2vh'};
         
         this.closButtonCSS = {      'position': 'absolute',      'right': '10',      'top': '5',      'color': 'black',      'background-color': 'transparent',      'border-style': 'none',      'font-size': '3vh',      'font-weight': '800', 'width': 'auto'  };
         
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
            $('#help-screen-overlay').click(function(){_this.showHideHelp(_this)});
            
        });
    }
    
    showHideHelp(_this){
        
        if (_this.showBoolean == true){
            // 
            $('#help-screen-overlay').fadeIn(500);
            $('#help-content-container').fadeIn(1000);
            
            _this.showBoolean = false;
        }
        else{
            
            $('#help-screen-overlay').fadeOut(1200);
            $('#help-content-container').fadeOut(800);
            
            _this.showBoolean = true;
        }
            
    }
}

// create new help menu for the page and load html elements and content into it 
var $helpMenu = new HelpMenuClass("../Classes/HelpViewClaim.php");