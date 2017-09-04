var $errorMessageArray = []; // stores any error messages when the form is processed
var $quantityProductClaim = 0; // stores number of products in the claim

// Checks the fields for vaild 
function processForm()
{
    var $repairAgentSet; // boolean used to tell if claim should be processed
    var $claimType = $('#claimdetails-claimtype-select').val();
    
    $errorMessageArray = []; // reset arry for each process of the form
    
    
    // check to make sure at least one product is in the claim before being submitted
    if ($quantityProductClaim == 0 )
    {
        showMessage('Error', 'A product must be in the claim before saving.');
        return false;
    }
    
    // check to make sure repair agent has been set if claim status is shipped
    // or complete for repair claims
    $repairAgentSet = checkRepairAgentSet();

    if ($repairAgentSet == true)
    {
        $('#claimdetails-repairagentid-input').prop("disabled", false);
    }
    else
    {
        return false;
    }
    
    // check inputs to make sure there vaild
    if ($claimType == 'Warranty' || $claimType == 'Non-Warranty')
    {
        checkCustomerInputs();
        checkFinailseRepairInputs();
        checkRepairAgentInputs();
    }
    

    // loop through the error array and print to the screen, if errors exist return false
    // if no error return true
    $arraylength = $errorMessageArray.length;

    // if errors exist - entries in array show error message box and messages
    if ($arraylength > 0)
    {
        var  $errorMsgs = "";

        // add each error to error msg string
        for (i = 0; i < $arraylength; i++)
        {
            $errorMsgs += "<h3 class=\"error-list\">" + $errorMessageArray[i] + "</h3> ";
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
        // on submit make sure inputs are enables so they will be posted
        $('#claimdetails-claimtype-select').prop("disabled", false);
        $('#claimdetails-supplierid-input').prop("disabled", false);
        $('#claimdetails-claimid-input').prop("disabled", false);
        
        id="claimdetails-claimtype-select"
        disableRepairAgentInputs(false);
        
        return true;
    }
}


// if claim status is shipped or complete, a repair agent must be present.
// Return true if repair agent present and false if not. Used for repair claims.
function checkRepairAgentSet()
{
    $claimType = $('#claimdetails-claimtype-select').val();
    $claimStatus =  $('#claimdetails-claimstatus-select').val();
    
    // check claim type is repair
    if ($claimType == 'Warranty' || $claimType == 'Non-Warranty')
    { 
        // check status
        if ($claimStatus == 'Shipped' || $claimStatus == 'Complete')
        {
            // if no repair agent set (null), then return false and display warning message
            if($('#claimdetails-repairagent-select').val() == null)
            {
                showMessage('Error', 'A Repair Agent is Required; when the claim status is \'Shipped\' or \'Complete\'.');

                return false;
            }
            else // if repair agent is set in the select menu return true
            {
                return true;
            }
        }
        else // if status not shipped or complete return true
        {
            return true;
        }
    }
    else
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


// vaildats the repair agent inputs are correct if its a new repair agent or
// updating existing, returns boolean
function checkRepairAgentInputs()
{
    var $repairAgentAction = $('#claimdetails-repairagenthidden-input').val();
    
    if($repairAgentAction == 'new' || $repairAgentAction == 'update')
    {
        var $nameRegex = new RegExp(/^[a-zA-Z0-9-_@&#%'\s]{2,40}$/);
        var $phoneRegex = new RegExp(/^[0-9\s]{8,20}$/);
        var $emailRegex = new RegExp(/^[a-zA-Z0-9-_\.]{1,40}@{1}[a-zA-Z0-9-_\.]{1,40}[\.]{1}[a-zA-Z0-9-_\.]{1,40}$/);
        var $unitHouseRegex = new RegExp(/^[0-9\/-\s]{1,11}$/);
        var $streetRegex = new RegExp(/^[a-zA-Z\s]{1,40}$/);
        var $suburbCityRegex = new RegExp(/^[a-zA-Z,\s]{1,70}$/);
        var $stateRegex =  new RegExp(/^[a-zA-Z\s]{1,40}$/);
        var $postcodeRegex = new RegExp(/^[0-9]{4}$/);
        var $supplierIDRegex = new RegExp(/^[0-9]{1,20}$/);
        
        var $name =  $('#claimdetails-repairagentname-input').val();
        var $phone = $('#claimdetails-repairagentphone-input').val();
        var $email = $('#claimdetails-repairagentemail-input').val();
        var $unitHouse = $('#claimdetails-repairagentunithouse-input').val();
        var $street = $('#claimdetails-repairagentstreet-input').val();
        var $suburbCity = $('#claimdetails-repairagentsuburbcity-input').val();
        var $state = $('#claimdetails-repairagentstate-input').val();
        var $postcode = $('#claimdetails-repairagentpostcode-input').val();
        
        // check name is vaild - if not puts error message in array
        if($nameRegex.test($name) == false)
        {
            $errorMessageArray.push("Repair Agent Name - Must contain letters only and be between 1 and 40 characters in length.");
            $('#claimdetails-repairagentname-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#claimdetails-repairagentname-input').css('background-color','intial');
        }
        
        // check phone is vaild - if not puts error message in array
        if($phoneRegex.test($phone) == false)
        {
            $errorMessageArray.push("Repair Agent Phone - Must contain numbers only and be between 8 and 20 characters in length.");
            $('#claimdetails-repairagentphone-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#claimdetails-repairagentphone-input').css('background-color','intial');
        }
        
        // check email; allow no email to be entered - if entered check for vaild type 
        if($email != "" && $emailRegex.test($email) == false)
        {
            $errorMessageArray.push("Repair Agent Email - Must be a vaild email type with @ and domain such as '.com'. Example 'bob@MyEmail.com'.");
            $('#claimdetails-repairagentemail-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#claimdetails-repairagentemail-input').css('background-color','intial');
        }
        
        // check unit/house number is vaild - if not puts error message in array
        if($unitHouseRegex.test($unitHouse) == false)
        {
            $errorMessageArray.push("Repair Agent Unit/House Number - Must contain numbers, spaces and slashes only. Must be between 1 and 11 characters in length.");
            $('#claimdetails-repairagentunithouse-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#claimdetails-repairagentunithouse-input').css('background-color','intial');
        }
        
        // check street is vaild - if not puts error message in array
        if($streetRegex.test($street) == false)
        {
            $errorMessageArray.push("Repair Agent Street - Must contain letters and spaces only. Must be between 1 and 40 characters in length.");
            $('#claimdetails-repairagentstreet-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#claimdetails-repairagentstreet-input').css('background-color','intial');
        }
        
        // check suburb is vaild - if not puts error message in array
        if($suburbCityRegex.test($suburbCity) == false)
        {
            $errorMessageArray.push("Repair Agent Suburb/City - Must contain letters and spaces only. Must be between 1 and 70 characters in length.");
            $('#claimdetails-repairagentsuburbcity-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#claimdetails-repairagentsuburbcity-input').css('background-color','intial');
        }
        
        // check state is vaild - if not puts error message in array
        if($stateRegex.test($state) == false)
        {
            $errorMessageArray.push("Repair Agent State - Must contain letters and spaces only. Must be between 1 and 40 characters in length.");
            $('#claimdetails-repairagentstate-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#claimdetails-repairagentstate-input').css('background-color','intial');
        }
        
        // check postcode input is vaild - if not puts error message in array
        if($postcodeRegex.test($postcode) == false)
        {
            $errorMessageArray.push("Repair Agent Postcode - Must contain numbers only and be 4 characters in length.");
            $('#claimdetails-repairagentpostcode-input').css('background-color', '#ffaeb0');
        }
        else
        {
            $('#claimdetails-repairagentpostcode-input').css('background-color','intial');
        }
    }
}


// uses a products keycode to retrieve a suppliers information from database
// and stores it into the supplier inputs
function changeSupplier($keycode)
{
    $.ajax(
            {
                type: 'POST',
                url: 'ClaimDetails.php', 
                data : { 
                        action: "change-supplier",
                        keycode: $keycode
                        },
                success: function(data)
            {
                // turn php array into json object
                var obj = JSON.parse(data);

                // if supplier information found assign to inputs
                if(obj['response'] == 'found') // print product to screen
                {
                    $('#claimdetails-supplierid-input').val(obj['supplierid']);
                    $('#claimdetails-suppliername-input').val(obj['name']);
                    $('#claimdetails-supplierphone-input').val(obj['phone']);
                    $('#claimdetails-supplieremail-input').val(obj['email']);
                    $('#claimdetails-supplieraddress-input').val(obj['address']);
                }
                else if (obj['response'] == 'query-error')
                {
                    showMessage("Error", "There was an internal error with the query. Please contact technical support.");

                }
                else if (obj['response'] == 'not-found')
                {
                    showMessage("Error", "Supplier not found in database. Please contact technical support.");
                }
            }
        });
}

// uses a products keycode to retrieve a suppliers information from database
// and stores it into the supplier inputs
function clearSupplierInputsCheck()
{
    // if supplier information found assign to inputs
    if ($quantityProductClaim == 0 ) // print product to screen
    {
        $('#claimdetails-supplierid-input').val('');
        $('#claimdetails-suppliername-input').val('');
        $('#claimdetails-supplierphone-input').val('');
        $('#claimdetails-supplieremail-input').val('');
        $('#claimdetails-supplieraddress-input').val('');
    }
}

// enables or disables repair agent inputs based on true or false statement provided
function disableRepairAgentInputs($boolean)
{
    $('#claimdetails-repairagentname-input').prop('disabled', $boolean);
    $('#claimdetails-repairagentphone-input').prop('disabled', $boolean);
    $('#claimdetails-repairagentemail-input').prop('disabled', $boolean);
    $('#claimdetails-repairagentunithouse-input').prop('disabled', $boolean);
    $('#claimdetails-repairagentstreet-input').prop('disabled', $boolean);
    $('#claimdetails-repairagentsuburbcity-input').prop('disabled', $boolean);
    $('#claimdetails-repairagentstate-input').prop('disabled', $boolean);
    $('#claimdetails-repairagentpostcode-input').prop('disabled', $boolean);
}

function resetRepairAgentInputColour()
{
    $('#claimdetails-repairagentname-input').css('background-color','intial');
    $('#claimdetails-repairagentphone-input').css('background-color','intial');
    $('#claimdetails-repairagentemail-input').css('background-color','intial');
    $('#claimdetails-repairagentunithouse-input').css('background-color','intial');
    $('#claimdetails-repairagentstreet-input').css('background-color','intial');
    $('#claimdetails-repairagentsuburbcity-input').css('background-color','intial');
    $('#claimdetails-repairagentstate-input').css('background-color','intial');
    $('#claimdetails-repairagentpostcode-input').css('background-color','intial');
}

// checks finalise repair inputs for valid data - records error msg if incorrect into
// global error message array
function checkFinailseRepairInputs()
{
    var $repairCostRegex = new RegExp(/^[0-9]{1,7}\.{0,1}[0-9]{0,2}$/);
    var $repairAuthorisedNameRegex = new RegExp(/^[a-zA-Z\s]{1,25}$/);
    var $repairMessageRegex = new RegExp(/^.{10,3000}$/);
    
    var $repairCost = $('#repair-cost-input').val();
    var $authorisedName = $('#return-authorized-name-input').val();
    var $repairMessage = $('#fault-textarea').val();
    
    $repairCost = $repairCost.replace("$", "");
    
    // check for vaild repair cost type or allow if blank
    if ($repairCostRegex.test($repairCost) == true || $repairCost == "")
    {
        $('#repair-cost-input').css('background-color', 'white');
    }
    else
    {
        $errorMessageArray.push("Repair Cost - Must contain full numbers and decimals only. Maxmium of 10 characters allowed. If no repair cost is available, leave blank.");
        $('#repair-cost-input').css('background-color', '#ffaeb0');
    }

    if ($repairAuthorisedNameRegex.test($authorisedName) == false)
    {
        $errorMessageArray.push("Authorised Name - Must Letters and spaces only. Maxmium of 25 characters.");
        $('#return-authorized-name-input').css('background-color', '#ffaeb0');
    }
    else
    {
        $('#return-authorized-name-input').css('background-color', 'white');
    }

    if ($repairMessageRegex.test($repairMessage) == false)
    {
        $errorMessageArray.push("Repair Message - Must contain 10-3000 characters.");
        $('#fault-textarea').css('background-color', '#ffaeb0');
    }
    else
    {
        $('#fault-textarea').css('background-color', 'white');
    }
}


// checks for vaild customer inputs and saves in into global error message array
function checkCustomerInputs()
{
    var $nameRegex = new RegExp(/^[a-zA-Z\s]{2,15}$/);
    var $phoneRegex = new RegExp(/^[0-9\s]{8,20}$/);
    var $emailRegex = new RegExp(/^[a-zA-Z0-9-_\.]{1,40}@{1}[a-zA-Z0-9-_\.]{1,40}[\.]{1}[a-zA-Z0-9-_\.]{1,40}$/);
    var $unitHouseRegex = new RegExp(/^[0-9\/\s]{1,9}$/);
    var $streetRegex = new RegExp(/^[a-zA-Z\s]{1,20}$/);
    var $suburbRegex = new RegExp(/^[a-zA-Z\s]{1,30}$/);
    var $stateRegex =  new RegExp(/^[a-zA-Z\s]{1,30}$/);
    var $postcodeRegex = new RegExp(/^[0-9]{4}$/);
    
    var $postcode = $('#customer-postcode-input').val();
    var $firstName = $('#customer-firstname-input').val();
    var $lastName = $('#customer-lastname-input').val();
    var $phone = $('#customer-phone-input').val();
    var $email = $('#customer-email-input').val();
    var $unitHouse = $('#customer-unithouse-input').val();
    var $street = $('#customer-street-input').val();
    var $suburbCity = $('#customer-suburbcity-input').val();
    var $state = $('#customer-state-input').val();
    var $postcode = $('#customer-postcode-input').val();

    if ($nameRegex.test($firstName) == false)
    {
        $errorMessageArray.push("Customer First Name - Must contain letters only and be between 1 and 15 characters in length.");

        $('#customer-firstname-input').css('background-color', 'initial');
    }
    else
    {
        $('#customer-firstname-input').css('background-color', 'white');
    }

    if ($nameRegex.test($lastName) == false)
    {
        $errorMessageArray.push("Customer Last Name - Must contain letters only and be between 1 and 15 characters in length.");
        $('#customer-lastname-input').css('background-color', '#ffaeb0');
    }
    else
    {
        $('#customer-lastname-input').css('background-color', 'white');
    }

    if ($phoneRegex.test($phone) == false)
    {
        $errorMessageArray.push("Customer Phone - Must contain numbers only and be between 8 and 20 characters in length.");
        $('#customer-phone-input').css('background-color', '#ffaeb0');
    }
    else
    {
        $('#customer-phone-input').css('background-color', 'white');
    }

    if ($emailRegex.test($email) == true || $email == "")
    {
        $('#customer-email-input').css('background-color', 'white');
    }
    else
    {
        $errorMessageArray.push("Customer Email- Must be a vaild email with a @ symbol and a full stop (.), otherwise leave blank if not available.");
        $('#customer-email-input').css('background-color', '#ffaeb0');
    }

    if ($unitHouseRegex.test($unitHouse) == false)
    {
        $errorMessageArray.push("Customer Unit/House number - Must contain numbers and spaces only, with maxmium of 9 characters.");
        $('#customer-unithouse-input').css('background-color', '#ffaeb0');
    }
    else
    {
        $('#customer-unithouse-input').css('background-color', 'white');
    }

    if ($streetRegex.test($street) == false)
    {
        $errorMessageArray.push("Customer Street - Must contain letters and spaces only, with a maxmium of 20 characters.");
        $('#customer-street-input').css('background-color', '#ffaeb0');
    }
    else
    {
        $('#customer-street-input').css('background-color', 'white');
    }

    if ($suburbRegex.test($suburbCity) == false)
    {
        $errorMessageArray.push("Customer City/Suburb - Must contain letters and spaces only, with a maxmium of 30 characters.");
        $('#customer-suburbcity-input').css('background-color', '#ffaeb0');
    }
    else
    {
        $('#customer-suburbcity-input').css('background-color', 'white');
    }

    if ($stateRegex.test($state) == false)
    {
        $errorMessageArray.push("Customer State - Must contain letters and spaces only, with a maxmium of 30 characters.");
        $('#customer-state-input').css('background-color', '#ffaeb0');
    }
    else
    {
        $('#customer-state-input').css('background-color', 'white');
    }

    if ($postcodeRegex.test($postcode) == false)
    {
        $errorMessageArray.push("Customer Postcode - Must contain numbers only and be 4 characters in length.");
        $('#customer-postcode-input').css('background-color', '#ffaeb0');
    }
    else
    {
        $('#customer-postcode-input').css('background-color', 'white');
    }
}


// displays warning about no products in claim quantity is zero, removes warning if more than zero
function displayNoProducts()
{
    if ($quantityProductClaim == 0)
    {
        $('#claimdetails-insertline').after("<div id=\"claimdetails-productcontainer-noproduct\"> No Products in Claim </div>");
    }
    else
    {
        $('#claimdetails-productcontainer-noproduct').remove();
    }
}

// ------------------------------------- document ready functions ------------------------------
$(document).ready(function(){

    var $claimStatus = $('#claimdetails-claimstatus-select').val();
    var $claimType = $('#claimdetails-claimtype-select').val();
    
    // when document loads count number of products based on number of elements
    // using the class 'claimdetails-product-container'; minus 1 off for the heading as its
    // not a product.
    $quantityProductClaim =  $(".claimdetails-product-container").length;
    
    // check if product controls should be active
    checkProductControls();
    
    checkRepairAgentControls();
    
    checkRepairAgentsAvailable();
    
    displayNoProducts();
   
    // when repair agent new button is clicked, make the fields blank
    $(document).on('click', '#claimdetails-repairagent-new-button', function(){
        
        // unlock inputs
        disableRepairAgentInputs(false);
        
        // make inputs blank
        clearRepairAgentInputs();
        
        // add a blank field to the select options
        $('#claimdetails-repairagent-select').prepend("<option value=\"new\" id=\"option-new\">New Repair Agent</option>");
        
        $('#claimdetails-repairagent-select').val('new');
        
        // set hidden input text to new - so when submitting add new repair agent will be called
        $('#claimdetails-repairagenthidden-input').val('new');
        
    });
    
    // when repair agent 'Update' button is clicked, unlock inputs for editing
    $(document).on('click', '#claimdetails-repairagent-update-button', function(){
        
        // unlock inputs
        disableRepairAgentInputs(false);
        
         // set hidden input text to update - so when submitting the page, the repair agents details will be updated
        $('#claimdetails-repairagenthidden-input').val('update');
    });
    
    // when the repair agent select-dropdown is changed, the function gets the repair agents details 
    // from the server and puts it into the input fields and disables them from being edited 
    $(document).on('click', '#claimdetails-repairagent-select', function(){
  
        // current value for the selected dropdown option
        var $repairAgentID = $(this).val();
        
        // only search for repair agent existing - not new
        if ($repairAgentID != 'new' && $repairAgentID != null)
        {
            // get the selected repair agents details
            getRepairAgentDetails($repairAgentID);
            
            // remove new option if there
            $('#option-new').remove();
            
            // disable inputs when changed
            disableRepairAgentInputs(true);
        }
        
         // set hidden input text to no action - so when submitting the page, no action will be required
        $('#claimdetails-repairagenthidden-input').val('no-action');
        
    });
    
    
    $(document).on('click', '#claimdetails-repairagent-delete-button', function(){
        
        
        var $repairAgentID = $('#claimdetails-repairagent-select').val();
        var $repairAgentName = $('#claimdetails-repairagentname-input').val();
        
        // reset repair agent inputs colour to white, incase new repair agent is
        // being deleted
        resetRepairAgentInputColour();
        
        // if repair agent is not new or null delete from database
        if ($repairAgentID != 'new' && $repairAgentID != null)
        {
            $.ajax({
                type: 'POST',
                url: 'ClaimDetails.php',
                data: {
                        action: 'remove-repairagent',
                        repairAgentID: $repairAgentID
                },
                success: function(data){

                    // turn php array into json object
                    var obj = JSON.parse(data);

                    if(obj['response'] == "removed")
                    {
                        showMessage('Success', 'Repair Agent: \"' + $repairAgentName +'\", was removed from the database.');

                        // remove repair agent from select menu
                        $('#repairagent-option-' + $repairAgentID).remove();

                        // get current selected repair agent id from select menu and retrieve
                        // it details from the database. The fill the information into the inputs
                        getRepairAgentDetails($('#claimdetails-repairagent-select').val());

                    }
                    else if (obj['response'] == 'not-found')
                    {
                        showMessage('Internal Error', 'Repair Agent not found in database. Contact tech support.')
                    }
                    else if (obj['response'] == 'invaild-repairid')
                    {
                        showMessage('Internal Error', 'Invaild Repair Agent ID. Contact tech support.');
                    }
                }
            });
            
            // disable inputs when delete button is clicked
            disableRepairAgentInputs(true);
        }
        else // if repair agent is new remove from drop-down and 
        {
            // remove new option if there
            $('#option-new').remove();
            
            // get the top drop down selection repair agent id
            $repairAgentID = $('#claimdetails-repairagent-select').val();
            
            // if deleting new repair agent option, make sure that there as at least
            // one repair agent left before searching database. If select menu is null
            // do not search that database as it will cause an error.
            if ($repairAgentID != null)
            {
                // load the repair agents information into input boxes
                getRepairAgentDetails($repairAgentID);
            }
            // if there is no repair agents for the supplier; set hidden input to no-action
            // so repair agent is not being updated in database - causing an error 
            else
            {
                $('#claimdetails-repairagenthidden-input').val('no-action');
            }
            
            // disable inputs when changed
            disableRepairAgentInputs(true);
        }
    });
    
    
    // checks claim status - if any claims status is not created disable
    // functionilty to add, remove or update products in a claim
    $(document).on('change', '#claimdetails-claimstatus-select', function(){
        
        // reset the current claim status
        $claimStatus = $(this).val();
        
        // do ra number check
        raNumberCheck();
        
        // check ra check box
        sendRACheckBox();
        
        // check if product controls should be active
        checkProductControls();
        
        // check if repair agent controls should be active
        checkRepairAgentControls();
    });
    
    
    // when the claim type drop down is change it will run funtions and update
    // global claimType
    $(document).on('change', '#claimdetails-claimtype-select', function(){
        
        // reset the current claim type
        $claimType = $('#claimdetails-claimtype-select').val();
        
        sendRACheckBox(); // checks wether RA check box should be disabled
        
        raNumberInputDisplayCheck();
        
        repairStatusChange();
    
    });
    
    
    // hides
    $(document).on('click', '.warning-confirm-button', function(){
            hideMessage();
    });
    
    
   // when add button is clicked product and quanity is sent to database 
    // response and html is returned for that item. If not found error code
    // is returned and proccessed as message box to user.
    $(document).on('click', '#claimdetails-addproduct-button', function(){

        var $keycode = $('#claimdetails-keycode-input').val().replace(' ', '');
        var $quantity = $('#claimdetails-quantity-input').val().replace(' ', '');
        var $keycodeRegex = new RegExp(/^[0-9]{1,9}$/);
        var $quantityRegex = new RegExp(/^[0-9]{1,6}$/);
        
        if ($keycodeRegex.test($keycode) == true)
        {
            if ($quantityRegex.test($quantity) == true)
            {
                $.ajax(
                {
                    type: 'POST',
                    url: 'ClaimDetails.php', 
                    data : { 
                            action: "addproduct",
                            keycode: $keycode,
                            quantity: $quantity
                            },
                    //datatype: "json",
                    success: function(data)
                {
                    // turn php array into json object
                    var obj = JSON.parse(data);
                    
                    if(obj['response'] == 'success') // print product to screen
                    {
                        $('#claimdetails-insertline').after(obj['html']);
                        $('#claimdetails-productdescription-input').val(obj['description']);
                        
                        // update the claim amount from the returned json obj
                        $('#claimdetails-claimamount-input').val(obj['claim-amount']);
                        
                        // if there is no proucts update the supplier fields
                        if ($quantityProductClaim == 0){
                            
                            changeSupplier($keycode);
                        }
                        
                        // add product to count
                        $quantityProductClaim ++;
                        
                        // checks if no product message should be displayed or not
                        // based on products in claim
                        displayNoProducts();
                    }
                    else if (obj['response'] == 'already-in-claim')
                    {
                        showMessage("Error","Product already exists in the claim");
                        
                    }
                    else if (obj['response'] == 'product-not-found')
                    {
                        showMessage("Error", "Product not found in database.");
                    }
                    else if (obj['response'] == 'session-expired')
                    {
                        showMessage("Error", "Session has expired.");
                    }
                    else if (obj['response'] == 'different-supplier')
                    {
                        showMessage("Error", "Product/s must be from same supplier.");
                    }
                }
                      
                });
            }
            else
            {
                showMessage("Error","Invaild Quantity provided.");
            }
        }
        else
        {
            showMessage("Error","Invaild Keycode.");
        }
    });
    
    
    // checks for when of the individual product remove  buttons is click, it then
    // runs removeProduct() function to remove the product
    $(document).on('click', '.claimdetails-removebutton-individual', function(){

        var $buttonID = $(this).attr('id');
        
        // removes 'product-container-' to get just get the keycode
        var $keycode = $buttonID.replace("remove-",""); 
        
        removeProduct($keycode); // run function to remove the product
    });
    
    
    // if product div is clicked - it will load the keycode, quantity and description
    // from the div into the relevant input boxes
    $(document).on('click', '.claimdetails-product-container', function(){
        
        var $keycode = $(this).attr('id').replace('product-container-', '');
        var $quantity = $('#quantity-'+ $keycode).text();
        var $description = $('#description-'+ $keycode).text();
        
        // removes bug with a space being added when loading into input
        //$quantity =  $quantity.replace(' ', ''); 

        $('#claimdetails-quantity-input').val($quantity);
        $('#claimdetails-productdescription-input').val($description);
        $('#claimdetails-keycode-input').val($keycode);
    });
    
    
    // checks for when of the main remove buttons in the product panel is click. It then
    // runs removeProduct() function to remove the product from session and screen, based
    // on the keycoded loaded in the keycode input box
    $(document).on('click', '#claimdetails-removeproduct-button', function(){

        var $keycode = $('#claimdetails-keycode-input').val(); 
        
        removeProduct($keycode); // run function to remove the product
    });
    
    
    // updates the product quantity in the session and returns the result
    $(document).on('click', '#claimdetails-updateproduct-button', function(){
        
        var $keycode = $('#claimdetails-keycode-input').val().replace(' ', '');
        var $quantity = $('#claimdetails-quantity-input').val().replace(' ', '');
        var $quantityRegex = new RegExp(/^[0-9]{1,6}$/); 
        var $keycodeRegex = new RegExp(/^[0-9]{1,9}$/);
        
        if ($keycodeRegex.test($keycode) == true)
        {
            if ($quantityRegex.test($quantity) == true)
            {
                $.ajax(
                {
                    type: 'POST',
                    url: 'ClaimDetails.php', 
                    data : { 
                            action: "updateproduct",
                            keycode: $keycode,
                            quantity: $quantity
                            },
                    success: function(data)
                {
                    // turn php array into json object
                    var obj = JSON.parse(data);

                    if(obj['response'] == 'product-updated')
                    {   
                        $('#quantity-'+ $keycode).text($quantity);
                        $('#claimdetails-productdescription-input').val(obj['description']);
                        
                        // update the claim amount from the returned json obj
                        $('#claimdetails-claimamount-input').val(obj['claim-amount']);
                    }
                    else if (obj['response'] == 'product-not-updated')
                    {
                        showMessage("Error","Product not updated.");
                    }
                }

                });
            }
            else
            {
                showMessage("Error","Invaild Quantity provided.");
            }
        }
        else
        {
            showMessage("Error","Invaild Keycode.");
        }
        
    });


    // checks wether the repair agents controls should be disabled or enabled
    // based on the current claim status. If product is shipped, complete or cancelled
    // a repair agent controls will be disabled.
    function checkRepairAgentControls()
    {
        if ($claimType == 'Warranty' || $claimType == 'Non-Warranty')
        { 
            if ($claimStatus != 'Created' && $claimStatus != 'RA Requested' && 
                $('#claimdetails-repairagent-select').val() != null)
            {
                $('#claimdetails-repairagent-new-button').prop("disabled", true);
                $('#claimdetails-repairagent-update-button').prop("disabled", true);
                $('#claimdetails-repairagent-delete-button').prop("disabled", true);
                $('#claimdetails-repairagent-select').prop("disabled", true);

                // always disable repair agent inputs on page load
                disableRepairAgentInputs(true);
            }
            else
            {
                $('#claimdetails-repairagent-new-button').prop("disabled", false);
                $('#claimdetails-repairagent-update-button').prop("disabled", false);
                $('#claimdetails-repairagent-delete-button').prop("disabled", false);
                $('#claimdetails-repairagent-select').prop("disabled", false);

                // always disable repair agent inputs on page load
                disableRepairAgentInputs(true);
            }
        }
    }

    // check to see if repair agent exists for supplier; if not disables select menu
    // for the repair agents as it will not be needed and stops error appearing.
    function checkRepairAgentsAvailable()
    {
        if ($('#claimdetails-repairagent-select').val() == null)
        {
            $('#claimdetails-repairagent-select').prop("disabled", true);
        }
    }


    // retreives a repair agent details based on a provided repair agent ID number
    // and places into the repair agent inputs
    function getRepairAgentDetails($repairAgentID)
    {
          $.ajax({
            type: 'POST',
            url: 'ClaimDetails.php',
            data: {
                    action: 'get-repairagent-details',
                    repairAgentID: $repairAgentID
            },
            success: function(data)
            {
                // turn php array into json object
                var obj = JSON.parse(data);

                if (obj['response'] == 'found')
                {
                    $('#claimdetails-repairagentid-input').val(obj['repairAgentID']);
                    $('#claimdetails-repairagentname-input').val(obj['name']);
                    $('#claimdetails-repairagentphone-input').val(obj['phone']);
                    $('#claimdetails-repairagentemail-input').val(obj['email']);
                    $('#claimdetails-repairagentunithouse-input').val(obj['unitHouseNumber']);
                    $('#claimdetails-repairagentstreet-input').val(obj['street']);
                    $('#claimdetails-repairagentsuburbcity-input').val(obj['suburbCity']);
                    $('#claimdetails-repairagentstate-input').val(obj['state']);
                    $('#claimdetails-repairagentpostcode-input').val(obj['postcode']);
                }
                else if (obj['response'] == 'not-found')
                {
                    showMessage("Internal Error", "Failed to find RepairAgent. Contact Tech Support.");
                }
                else if (obj['response'] == 'invaild-repairagent-id')
                {
                    showMessage("Internal Error", "Invaild RepairAgent ID. Contact Tech Support.");  
                }
            }
        });
    }

    // clears all the repair agent inputs
    function clearRepairAgentInputs()
    {
        $('#claimdetails-repairagentid-input').val('');
        $('#claimdetails-repairagentname-input').val('');
        $('#claimdetails-repairagentphone-input').val('');
        $('#claimdetails-repairagentemail-input').val('');
        $('#claimdetails-repairagentunithouse-input').val('');
        $('#claimdetails-repairagentstreet-input').val('');
        $('#claimdetails-repairagentsuburbcity-input').val('');
        $('#claimdetails-repairagentstate-input').val('');
        $('#claimdetails-repairagentpostcode-input').val('');
    }

    // checks wether the ra check box should be disabled - if type is
    // non-warranty or status not created disable
    function sendRACheckBox()
    {
        if ($claimType == 'Non-Warranty')
        {
            $('#ra-checkbox').prop("disabled", true);
            $('#ra-checkbox').attr('checked', false);
        }
        else if ($claimStatus != 'Created')
        {
            $('#ra-checkbox').prop("disabled", true);
            $('#ra-checkbox').attr('checked', false);
        }
        else
        {
            $('#ra-checkbox').prop("disabled", false);
        }
    }

    // hides or shows the ra number input
    function raNumberInputDisplayCheck()
    {
        if ($claimType == 'Warranty')
        {
            $('#claimdetails-ranumber-div').show();
        }
        else
        {
            $('#claimdetails-ranumber-div').hide();
        }
    }


    // hides the message box
    function hideMessage()
    {
        $('.warning-screen-cover').fadeOut(500);
        $('.warning-confirm-container').fadeOut(500);
    }



    // enables or disables repair agent inputs based on true or false statement provided
    function disableRepairAgentInputs($boolean)
    {
        $('#claimdetails-repairagentname-input').prop('disabled', $boolean);
        $('#claimdetails-repairagentphone-input').prop('disabled', $boolean);
        $('#claimdetails-repairagentemail-input').prop('disabled', $boolean);
        $('#claimdetails-repairagentunithouse-input').prop('disabled', $boolean);
        $('#claimdetails-repairagentstreet-input').prop('disabled', $boolean);
        $('#claimdetails-repairagentsuburbcity-input').prop('disabled', $boolean);
        $('#claimdetails-repairagentstate-input').prop('disabled', $boolean);
        $('#claimdetails-repairagentpostcode-input').prop('disabled', $boolean);
    }

    // clears all the repair agent inputs
    function clearRepairAgentInputs()
    {
        $('#claimdetails-repairagentid-input').val('');
        $('#claimdetails-repairagentname-input').val('');
        $('#claimdetails-repairagentphone-input').val('');
        $('#claimdetails-repairagentemail-input').val('');
        $('#claimdetails-repairagentunithouse-input').val('');
        $('#claimdetails-repairagentstreet-input').val('');
        $('#claimdetails-repairagentsuburbcity-input').val('');
        $('#claimdetails-repairagentstate-input').val('');
        $('#claimdetails-repairagentpostcode-input').val('');
    }

    // checks wether the ra check box should be disabled - if type is
    // non-warranty or status not created disable
    function sendRACheckBox()
    {
        if ($claimType == 'Non-Warranty')
        {
            $('#ra-checkbox').prop("disabled", true);
            $('#ra-checkbox').attr('checked', false);
        }
        else if ($claimStatus != 'Created')
        {
            $('#ra-checkbox').prop("disabled", true);
            $('#ra-checkbox').attr('checked', false);
        }
        else
        {
            $('#ra-checkbox').prop("disabled", false);
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


    // hides or shows the ra number input
    function raNumberInputDisplayCheck()
    {
        if ($claimType == 'Warranty')
        {
            $('#claimdetails-ranumber-div').show();
        }
        else
        {
            $('#claimdetails-ranumber-div').hide();
        }
    }


    // hides the message box
    function hideMessage()
    {
        $('.warning-screen-cover').fadeOut(500);
        $('.warning-confirm-container').fadeOut(500);
    }


    // enables or disables product controls based on status and claim type
    function checkProductControls()
    {
        if ($claimType == 'Warranty')
        {
            productControls(false); // disable
        }
        else if ($claimType == 'Non-Warranty')
        {
            productControls(false); // disable
        }
        else if($claimStatus == 'Created')
        {
             productControls(true); // enable
        }
        else
        {
            productControls(false); // disable
        }
    }


     // removes product from the session and the div that contains the product
    // in the product list
    function removeProduct($keycode)
    {
        var $keycodeRegex = new RegExp(/^[0-9]{1,9}$/);

        if ($keycodeRegex.test($keycode) == true)
        {
            $keycode = $keycode.replace(' ', '');

            $.ajax(
            {
                type: 'POST',
                url: 'ClaimDetails.php', 
                data : { 
                        action: "removeproduct",
                        keycode: $keycode
                        },
                success: function(data)
            {
                // turn php array into json object
                var obj = JSON.parse(data);

                if(obj['response'] == 'product-removed')
                {
                    $('#product-container-'+ $keycode).remove();

                    // update the claim amount from the returned json obj
                    $('#claimdetails-claimamount-input').val(obj['claim-amount']);
                    
                     // subtract product count by 1
                    $quantityProductClaim --;
                    
                    // clears supplier inputs if zero products in claim
                    clearSupplierInputsCheck();
                    
                    // checks if no product message should be displayed or not
                    // based on products in claim
                    displayNoProducts();
                    
                }
                else if (obj['response'] == 'product-not-removed')
                {
                    showMessage("Error","Product not removed.");
                }
            }

            });
        }
        else
        {
            showMessage("Error","Invaild Keycode provided.");
        }
    }


    // enables or disables the product add, remove, update buttons based on the 
    // boolean value
    function productControls($enableBoolean)
    {
        if ($enableBoolean == true)
        {
            $('#claimdetails-addproduct-button').prop("disabled", false);
            $('#claimdetails-updateproduct-button').prop("disabled", false);
            $('#claimdetails-removeproduct-button').prop("disabled", false);
            $('.claimdetails-removebutton-individual').prop("disabled", false);
        }
        else
        {
            $('#claimdetails-addproduct-button').prop("disabled", true);
            $('#claimdetails-updateproduct-button').prop("disabled", true);
            $('#claimdetails-removeproduct-button').prop("disabled", true);
            $('.claimdetails-removebutton-individual').prop("disabled", true);
        }
    }


    // checks if ra number is provided if claim status is shipped or complete
    // as it should be provided if claim has left the store. 
    function raNumberCheck()
    {
        if ($claimType == 'Warranty')
        {
            if($claimStatus == 'Shipped' || $claimStatus == 'Complete')
            {
                var $raValue = $('#claimdetails-ranumber-input').val();

                if( $raValue == null || $raValue == "")
                {
                    showMessage("Warning","A Return Authorised Number is required before shipping the claim.");
                }
            }
        }
    }


    // if claim type non-warranty removes the 'Ra Requested Option'
    function repairStatusChange()
    {
        // if claim type is non-warranty remove ra-requested option
        if ($claimType == 'Non-Warranty')
        {
            var $nonWarrantyOptions = "<option value=\"Created\">Created</option> <option value=\"Shipped\">Shipped</option> <option value=\"Complete\">Complete</option> <option value=\"Cancelled\">Cancelled</option>";

            $('#claimdetails-claimstatus-select').html($nonWarrantyOptions);
        }
        else if ($claimType == 'Warranty')
        {
            var $warrantyOptions = "<option value=\"Created\">Created</option> <option value=\"RA Requested\">RA Requested</option> <option value=\"Shipped\">Shipped</option> <option value=\"Complete\">Complete</option> <option value=\"Cancelled\">Cancelled</option>";

            $('#claimdetails-claimstatus-select').html($warrantyOptions);
        }
    }
    
    
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
var $helpMenu = new HelpMenuClass("../Classes/HelpNewStoreClaim.php");