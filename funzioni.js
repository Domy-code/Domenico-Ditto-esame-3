function fnModifica() {
    let parametri = new URLSearchParams(window.location.search);
    parametri.set("op", "MOD");
    const url = window.location.href + parametri;
    window.location.search = parametri.toString();
}
function fnAggiungi() {
    let parametri = new URLSearchParams(window.location.search);
    parametri.set("op", "ADD");
    const url = window.location.href + parametri;
    window.location.search = parametri.toString();
}
function fnCancella() {
    if (confirm("Sei sicuro di voler cancellare l' elemento?")) {
      let url = new URL (window.location);
      url.searchParams.set('op','CANC')
       window.location=url;
        
    };
}

function fnLogout(){
  let parametri = new URLSearchParams(window.location.search);
  parametri.set("op", "LOGOUT");
  const url = window.location.href + parametri;
  window.location.search = parametri.toString();
  console.log(windo.location.search);
}

function fnReturn(){
  let url = new URL (window.location);
  url.searchParams.set('op','VIS')
   window.location=url;

}

function fnShowPassword(){
    var x = document.getElementById("passwordLogin");
    var imgElement = document.getElementById('mostraPassword');
    var img1 = "./img/hide.png";
  var img2 = "./img/show.png";
  if (x.type === "password") {
    x.type = "text";
    imgElement.src = img1;
  } else {
    x.type = "password";
    imgElement.src = img2;
  }
}

function privacyMsgError() {
  document.getElementById("privacyError").innerHTML = "Accetta l' informativa sulla privacy";
}

function matchPassword(){
  let password = document.getElementById("password");
  let ripPassword = document.getElementById("ripPassword");
  if (ripPassword.value == password.value){
    ripPassword.style.borderBottomColor="#2DCA07";
    password.style.borderBottomColor="#2DCA07";
  }else{
    ripPassword.style.borderBottomColor="#afafaf";
    password.style.borderBottomColor="#afafaf";
  }
}

