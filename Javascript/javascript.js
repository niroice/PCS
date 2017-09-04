// opens the repair form in a new window  and loads the main menu page 
// when print repair order selected
function openRepairForm($loadpage) 
{
    window.open('loadRepairForm.php');
    
    window.location.href = $loadpage;
}