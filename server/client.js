function addinserver(event) {
  event.preventDefault();
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "http://45.145.6.18:3000/add", true);
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      console.log(xhr.status);
      console.log(xhr.responseText);
    }
  };
  var data = JSON.stringify({
    num1: parseInt(document.getElementById("num1").value),
    num2: parseInt(document.getElementById("num2").value),
  });
  xhr.send(data);

  xhr.onload = function () {
    if (xhr.status === 200) {
      let response = JSON.parse(xhr.responseText);
      console.log(`Sum is: ${response.sum}`); // Server response
    } else {
      console.error("Request failed");
    }
  };
}

function subinserver() {
  // client.js
  const xhr = new XMLHttpRequest();

  xhr.open('GET', '45.145.6.18/executeFunction', true);

  xhr.onload = function () {
    if (xhr.status === 200) {
      console.log(xhr.responseText); // Server response
    } else {
      console.error('Request failed');
    }
  };

  xhr.send();
}
