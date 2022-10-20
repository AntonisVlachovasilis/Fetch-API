document.querySelector("#validation_form").addEventListener("submit", (e) => {
  e.preventDefault();
  let form = document.querySelector("#registration_form");
  const data = new URLSearchParams();
  for (const p of new FormData(form)) {
    data.append(p[0], p[1]);
  }
  console.log(data);
  fetch("http://localhost/step2/projectB.php", {
    method: "POST",
    body: data,
  })
    .then((response) => response.json())
    .then((response) => {
      console.log(response);
      if (response.result == true) {
        console.log(response.redirect);
        window.location.replace(response.redirect);
      } else {
        document.querySelector(".nameError").innerHTML =
          response.message["nameError"];
        document.querySelector(".surnameError").innerHTML =
          response.message["surnameError"];
        document.querySelector(".phoneError").innerHTML =
          response.message["phoneError"];
        document.querySelector(".mailError").innerHTML =
          response.message["emailError"];
        document.querySelector(".dateError").innerHTML =
          response.message["dateError"];
      }
    })
    .catch((error) => console.log("Error"));
});
