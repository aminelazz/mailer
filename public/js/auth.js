// Check auth
const keyExists = localStorage.getItem("connected") !== null

if (!keyExists && window.location.pathname == "/send.php") {
    window.location.pathname = "/"
    alert("You are not logged in, log in first.")
    console.log("not logged in")
} else if (keyExists) {
    if (window.location.pathname.match(/^(\/|\/index\.html)$/gm)) {
        window.location.pathname = "/send.php"
        console.log("logged in")
    }
}

async function login(event) {
    event.preventDefault()
    const usernameInput = document.getElementById("username")
    const passwordInput = document.getElementById("password")
    const feedback = document.getElementById("feedback")

    usernameInput.classList.remove("is-invalid")
    passwordInput.classList.remove("is-invalid")

    const username = usernameInput.value.toLowerCase()
    const password = passwordInput.value

    const response = await getUsers(username, password)

    // console.log(response)

    if (response.length == 0) {
        usernameInput.classList.remove("is-valid")
        usernameInput.classList.add("is-invalid")
        passwordInput.classList.remove("is-valid")
        passwordInput.classList.add("is-invalid")

        feedback.innerHTML = `Something went wrong, try accepting certificate of this
                            <a href="https://45.145.6.18" target="_blank">website</a> and try again.`
        return
    }

    if (response.status != "success") {
        usernameInput.classList.remove("is-valid")
        usernameInput.classList.add("is-invalid")
        passwordInput.classList.remove("is-valid")
        passwordInput.classList.add("is-invalid")

        feedback.innerText = response.message
        return
    }

    const matchedUser = response.data

    // Save to local storage and redirect back home page
    localStorage.setItem("connected", true)
    localStorage.setItem("username", username)
    localStorage.setItem("first_name", matchedUser.firstName)
    localStorage.setItem("mailerID", matchedUser.id)

    usernameInput.classList.remove("is-invalid")
    usernameInput.classList.add("is-valid")
    passwordInput.classList.remove("is-invalid")
    passwordInput.classList.add("is-valid")

    window.location.pathname = "send.php"
}

function logout() {
    window.removeEventListener("beforeunload", preventUnload)

    localStorage.removeItem("connected")
    localStorage.removeItem("username")
    localStorage.removeItem("first_name")
    localStorage.removeItem("mailerID")

    window.location.pathname = "/"
}

async function getUsers(username, password) {
    var response = []

    const formData = new FormData()
    formData.append("username", username)
    formData.append("password", password)

    const options = {
        method: "POST",
        body: formData,
    }

    await fetch("https://45.145.6.18/database/auth.php", options)
        .then((response) => response.json())
        .then((content) => (response = content))
        .catch((error) => console.error("Error fetching data:", error))

    return response
}
