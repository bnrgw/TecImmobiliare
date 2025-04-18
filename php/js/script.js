document.addEventListener("DOMContentLoaded", function() {

  const menu = document.querySelector(".menu_hamb");
  const hamb = document.querySelector(".hamb_menu");

  function openCloseMenu() {
    if(menu.classList.contains("show_menu")) {
        menu.classList.remove("show_menu");
        menu.classList.add("close_menu");
        hamb.classList.remove("displayf");
    }
    else {
        if(menu.classList.contains("close_menu"))
            {
                menu.classList.remove("close_menu");
                menu.classList.add("show_menu");
                hamb.classList.add("displayf");

            }
            else {
                menu.classList.add("show_menu");
                hamb.classList.add("displayf");
            }
            

    }
    
}


  hamb.addEventListener("click", openCloseMenu);

 //Ottengo nome del file
  const currentUrl = window.location.pathname.split("/").pop();

  // Seleziona tutti gli elementi <a> nei menu di navigazione
  const navLinks = document.querySelectorAll('nav ul.menu_hamb li a, nav ul.menu li a');

  // Scorro ogni link
  navLinks.forEach(link => {
      // Confronto il nome del file del URL con quello del link
      if (link.getAttribute('href').split("/").pop() === currentUrl) {
          
        // Aggiungo la classe "active"
          link.parentElement.classList.add("active");
      }
  });

});

//controllo input

function controllaIscrizione(event) {

  let ele = document.getElementById("message");
  ele.classList.remove("success");
  ele.scrollIntoView();

  let nome = document.getElementById("name");
  let cognome = document.getElementById("surname");
  let email = document.getElementById("email");
  let password = document.getElementById("password");
  let confirm_password = document.getElementById("confirm_pwd");

  nome.value = nome.value.trim();
  cognome.value = cognome.value.trim();
  email.value = email.value.trim();
  password.value = password.value.trim();
  confirm_password.value = password.value.trim();

  if (nome.value && cognome.value && email.value && password.value && confirm_password.value){

        if (!controllaNomeCognome(nome.value)) {
          ele.innerHTML = "Errore: Il nome e cognome non devono contenere più di 32 caratteri.";
          ele.classList.add("error");
          nome.select();
          event.preventDefault();
          return false;
        }
        if (!controllaNomeCognome(cognome.value)) {
          ele.innerHTML = "Errore: Il nome e cognome  non devono contenere più di 32 caratteri.";
          ele.classList.add("error");
          cognome.select();
          event.preventDefault();
          return false;
        }
        if (!controllaEmail(email.value)) {
          ele.innerHTML = "Errore: L' email non è valida";
          ele.classList.add("error");
          email.select();
          event.preventDefault();
          return false;
        }
        if (!controllaPassword(password.value)) {
          ele.innerHTML = "Errore: La password deve essere almeno di 4 caratteri";
          ele.classList.add("error");
          password.select();
          event.preventDefault();
          return false;
        }
        if (!controllaPassword(confirm_password.value)) {
          ele.innerHTML = "Errore: La password deve essere almeno di 4 caratteri";
          ele.classList.add("error");
          password.select();
          event.preventDefault();
          return false;
        }
        if (!uguaglianzaPassword(password.value, confirm_password.value)) {
          ele.innerHTML = "Errore: La password deve essere uguale a quella scritta sopra";
          ele.classList.add("error");
          confirm_password.select();
          event.preventDefault();
          return false;
        }
        return true;
    } 
    else {
        ele.innerHTML = "Errore: Riempire tutti i campi.";
        ele.classList.add("error");
        event.preventDefault();
        return false;
    }
}

function controllaAccesso(event) {

  let ele = document.getElementById("message");
  ele.classList.remove("success");
  ele.scrollIntoView();

  let email = document.getElementById("email");
  let password = document.getElementById("password");

  email.value = email.value.trim();
  password.value = password.value.trim();

  if (email.value && password.value) {
    
    if (!controllaEmail(email.value)) {
      ele.innerHTML = "Errore: La mail non è valida";
      ele.classList.add("error");
      email.select();
      event.preventDefault();
      return false;
    }
    if (!controllaPassword(password.value)) {
      ele.innerHTML = "Errore: La password deve essere almeno di 4 caratteri";
      ele.classList.add("error");
      password.select();
      event.preventDefault();
      return false;
    }
    return true;
  }
  else{
    
    ele.innerHTML = "Errore: Riempire tutti i campi.";
    ele.classList.add("error");
    event.preventDefault();
    return false;
  }
}

function controllaCambioInformazioni(event) {

  let ele = document.getElementById("message");
  ele.classList.remove("success");
  ele.scrollIntoView();

  let nome = document.getElementById("new_name");
  let cognome = document.getElementById("new_surname");

  nome.value = nome.value.trim();
  cognome.value = cognome.value.trim();

  if (nome.value && cognome.value) {
    if (!controllaNomeCognome(nome.value)) {
      ele.innerHTML = "Errore: Il nome e cognome  non devono contenere più di 32 caratteri.";
      ele.classList.add("error");
      nome.select();
      event.preventDefault();
      return false;
    }
    if (!controllaNomeCognome(cognome.value)) {
      ele.innerHTML = "Errore: Il nome e cognome  non devono contenere più di 32 caratteri.";
      ele.classList.add("error");
      cognome.select();
      event.preventDefault();
      return false;
    }

    return true;
  } 
  else {
    ele.innerHTML = "Errore: Riempire tutti i campi.";
    ele.classList.add("error");
    event.preventDefault();
    return false;
  }
}

function controllaCambioPassword(event) {

  let ele = document.getElementById("message");
  ele.classList.remove("success");
  ele.scrollIntoView();

  let vpassword = document.getElementById("old_password");
  let npassword = document.getElementById("new_password");
  let rnpassword = document.getElementById("confirm_password");

  /*vpassword.value = vpassword.value.trim();
  npassword.value = npassword.value.trim();
  rnpassword.value = rnpassword.value.trim();*/

  if (vpassword.value && npassword.value && rnpassword.value) {
    if (!controllaPassword(vpassword.value)) {
      ele.innerHTML = "Errore: La password deve essere almeno di 4 caratteri";
      ele.classList.add("error");
      vpassword.select();
      event.preventDefault();
      return false;
    }
    if (!controllaPassword(npassword.value)) {
      ele.innerHTML = "Errore: La password deve essere almeno di 4 caratteri";
      ele.classList.add("error");
      npassword.select();
      event.preventDefault();
      return false;
    }
    if (!disuguaglianzaPassword(vpassword.value, npassword.value)) {
      ele.innerHTML = "Errore: La nuova password e la vecchia non devono essere uguali";
      ele.classList.add("error");
      npassword.select();
      event.preventDefault();
      return false;
    }
    if (!uguaglianzaPassword(npassword.value, rnpassword.value)) {
      ele.innerHTML = "Errore: La password deve essere uguale a quella scritta sopra";
      ele.classList.add("error");
      rnpassword.select();
      event.preventDefault();
      return false;
    }
    return true;
  } 
  else {
    ele.innerHTML = "Errore: Riempire tutti i campi.";
    ele.classList.add("error");
    event.preventDefault();
    return false;
  }
}

function controllaEmail(input) {

  let regex = new RegExp("/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,})+$");
  var atSymbol = input.indexOf("@");
  var dot = input.indexOf(".");

  if (atSymbol < 1 || dot <= atSymbol + 2 || dot == input.length - 1) {
    return false;
  }

  if (regex.test(input)) {
    return false;
  }
  return true;
}

function controllaPassword(input) {
  if (input.length >= 4) {
    return true;
  }
  return false;
}

function uguaglianzaPassword(input1, input2) {
  if (input1 == input2) {
    return true;
  }
  return false;
}

function disuguaglianzaPassword(input1, input2) {
  if (input1 == input2) {
    return false;
  }
  return true;
}

function controllaNomeCognome(input) {
  if (input.length > 32) {
    return false;
  }
  return true;
}



function controllaReview(event) {

  let ele = document.getElementById("message");
  ele.classList.remove("success");
  ele.scrollIntoView();

  let content = document.getElementById("review");

  content.value = content.value.trim();

  if (content.value) {
    return true;
  } 
  else {
    ele.innerHTML = "Errore: Recensione è vuota";
    ele.classList.add("error");
    event.preventDefault();
    return false;
  }
}

