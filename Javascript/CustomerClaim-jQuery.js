    var $nameRegex = new RegExp(/^[a-zA-Z\s]{2,15}$/);
    var $phoneRegex = new RegExp(/^[0-9\s]{8,20}$/);
    var $emailRegex = new RegExp(/^[a-zA-Z0-9-_\.]{1,40}@{1}[a-zA-Z0-9-_\.]{1,40}[\.]{1}[a-zA-Z0-9-_\.]{1,40}$/);
    var $unitHouseRegex = new RegExp(/^[0-9\/\s]{1,9}$/);
    var $streetRegex = new RegExp(/^[a-zA-Z\s]{1,20}$/);
    var $suburbRegex = new RegExp(/^[a-zA-Z\s]{1,30}$/);
    var $stateRegex =  new RegExp(/^[a-zA-Z\s]{1,30}$/);
    var $postcodeRegex = new RegExp(/^[0-9]{4}$/);
    var $repairCostRegex = new RegExp(/^[0-9]{1,7}\.{0,1}[0-9]{0,2}$/);
    var $repairAuthorisedNameRegex = new RegExp(/^[a-zA-Z\s]{1,25}$/);
    var $repairMessageRegex = new RegExp(/^.{10,3000}$/);

    var $errorMessagesArray = [];
    
   
    
    // checks for vaild firstname
    function checkFirstName()
    {
        $firstName = $('#customer-firstname-input').val();
        
        if ($nameRegex.test($firstName) == false)
        {
            $errorMessagesArray.push("First Name - Must contain letters only and be between 1 and 15 characters in length.");
            
            $('#customer-firstname-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#customer-firstname-input').css('background-color', 'white');
        }
    }

    // checks for vaild last name
    function checkLastName()
    {
        $lastName = $('#customer-lastname-input').val();
        
        if ($nameRegex.test($lastName) == false)
        {
            $errorMessagesArray.push("Last Name - Must contain letters only and be between 1 and 15 characters in length.");
            $('#customer-lastname-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#customer-lastname-input').css('background-color', 'white');
        }
    }

    // checks for vaild phone number
    function checkPhone()
    {
        $phone = $('#customer-phone-input').val();
        
        if ($phoneRegex.test($phone) == false)
        {
            $errorMessagesArray.push("Phone - Must contain numbers only and be between 8 and 20 characters in length.");
            $('#customer-phone-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#customer-phone-input').css('background-color', 'white');
        }
    }

    // checks for vaild Email type or allows null value
    function checkEmail()
    {
        $email = $('#customer-email-input').val();
        
        if ($emailRegex.test($email) == true || $email == "")
        {
            $('#customer-email-input').css('background-color', 'white');
        }
        else
        {
            $errorMessagesArray.push("Email- Must be a vaild email with a @ symbol and a full stop (.), otherwise leave blank if not available.");
            $('#customer-email-input').css('background-color', '#ffaeb0');
            
            
        }
    }


    // checks for vaild unit house number
    function checkUnitHouse()
    {
        $unitHouse = $('#customer-unithouse-input').val();
        
        if ($unitHouseRegex.test($unitHouse) == false)
        {
            $errorMessagesArray.push("Unit/House number - Must contain numbers and spaces only, with maxmium of 9 characters.");
            $('#customer-unithouse-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#customer-unithouse-input').css('background-color', 'white');
        }
    }

    // checks for street string
    function checkStreet()
    {
        $street = $('#customer-street-input').val();

        if ($streetRegex.test($street) == false)
        {
            $errorMessagesArray.push("Street - Must contain letters and spaces only, with a maxmium of 20 characters.");
            $('#customer-street-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#customer-street-input').css('background-color', 'white');
        }
    }

     // checks for suburb/city
    function checkSuburbCity()
    {
        $suburbCity = $('#customer-suburbcity-input').val();

        if ($suburbRegex.test($suburbCity) == false)
        {
            $errorMessagesArray.push("City/Suburb - Must contain letters and spaces only, with a maxmium of 30 characters.");
            $('#customer-suburbcity-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#customer-suburbcity-input').css('background-color', 'white');
        }
    }


    // checks for vaild state string
    function checkState()
    {
        $state = $('#customer-state-input').val();

        if ($stateRegex.test($state) == false)
        {
            $errorMessagesArray.push("State - Must contain letters and spaces only, with a maxmium of 30 characters.");
            $('#customer-state-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#customer-state-input').css('background-color', 'white');
        }
    }


    // checks for vaild postcode type
    function checkPostcode()
    {
        $postcode = $('#customer-postcode-input').val();

        if ($postcodeRegex.test($postcode) == false)
        {
            $errorMessagesArray.push("Postcode - Must contain numbers only and be 4 characters in length.");
            $('#customer-postcode-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#customer-postcode-input').css('background-color', 'white');
        }
    }

    // checks for vaild repair cost value or allows blank values
    function checkRepairCost()
    {
        $repairCost = $('#repair-cost-input').val();
        
        // if string not empty remove dollar sign if there
        //if ($repairCost != "")
        //{
            $repairCost = $repairCost.replace("$", "");
        //}

        // check for vaild repair cost type or allow if blank
        if ($repairCostRegex.test($repairCost) == true || $repairCost == "")
        {
            $('#repair-cost-input').css('background-color', 'white');
        }
        else
        {
            $errorMessagesArray.push("Repair Cost - Must contain full numbers and decimals only. Maxmium of 10 characters allowed. If no repair cost is available, leave blank.");
            $('#repair-cost-input').css('background-color', '#ffaeb0');
            
        }
    }

    // checks for vaild authorise string
    function checkAuthorisedName()
    {
        $authorisedName = $('#return-authorized-name-input').val();

        if ($repairAuthorisedNameRegex.test($authorisedName) == false)
        {
            $errorMessagesArray.push("Authorised Name - Must Letters and spaces only. Maxmium of 25 characters.");
            $('#return-authorized-name-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#return-authorized-name-input').css('background-color', 'white');
        }
    }


    // checks for vaild repair message
    function checkRepairMessage()
    {
        $repairMessage = $('#fault-textarea').val();

        if ($repairMessageRegex.test($repairMessage) == false)
        {
            $errorMessagesArray.push("Repair Message - Must contain 10-3000 characters.");
            $('#fault-textarea').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#fault-textarea').css('background-color', 'white');
        }
    }
    
    
    function validateForm()
    {
        var $errorMsgs = "<h2 class=\"error-heading\">Error - invaild fields provided:</h2>";
        var $arraylength;
        
        // reset the html in the error msg box, so message dont show twice
        $('#customerclaim-error-container').html();
        $errorMessagesArray = []; // reset the array as well stop double messages
        
        // run check functions for each input
        checkRepairMessage();
        checkFirstName();
        checkLastName();
        checkPhone();
        checkEmail();
        checkUnitHouse();
        checkStreet();
        checkSuburbCity();
        checkState();
        checkPostcode();
        checkRepairCost();
        checkAuthorisedName();
        
        
        $arraylength = $errorMessagesArray.length;
        
        // if errors exist - entries in array show error message box and messages
        if ($arraylength > 0)
        {
            // add each error to error msg string
            for (i = 0; i < $arraylength; i++)
            {
                $errorMsgs += "<h3 class=\"error-list\">" + $errorMessagesArray[i] + "</h3> ";
            }
            
            $('#customerclaim-error-container').html($errorMsgs);
            $('#customerclaim-error-container').fadeIn(1500);
            
            // scroll to error box - so it will be seen straight away - animates
             $('html, body').animate({
                                        scrollTop: $("#customerclaim-error-container").offset().top
                                    }, 2000);
            
            return false;
        }
        else // if no error return true to process the form
        {
            return true;
        }
    }
    
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

    // loads the main menu when cancel button is clicked
    function loadMainMenu(){
        window.location = "../MainMenu.php";
    }


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
var $helpMenu = new HelpMenuClass("../Classes/HelpCustomerClaim.php");
