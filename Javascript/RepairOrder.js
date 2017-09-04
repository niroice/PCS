document.onload = function() {
    
    document.getElementById('printagain-button').addEventListener("click", printRepairForm);
    document.getElementById('mainmenu-button').addEventListener("click", loadMainMenu);
   
}

 // launches the print browser module
function printRepairForm(){
    window.print();
}


// loads the main menu page
function loadMainMenuPage(){

    window.location = "../MainMenu.php";
}