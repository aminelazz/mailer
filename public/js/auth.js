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

    const users = await getUsers()

    const matchedUser = users.find(
        (user) => user.username === username && user.password === password
    )

    if (matchedUser) {
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
    } else {
        if (!matchedUser) {
            usernameInput.classList.remove("is-valid")
            usernameInput.classList.add("is-invalid")
            passwordInput.classList.remove("is-valid")
            passwordInput.classList.add("is-invalid")
        } else {
            usernameInput.classList.remove("is-valid")
            usernameInput.classList.add("is-invalid")
            passwordInput.classList.remove("is-valid")
            passwordInput.classList.add("is-invalid")

            feedback.value = "An error has occured"
        }
    }
}

function logout() {
    localStorage.removeItem("connected")
    localStorage.removeItem("username")
    localStorage.removeItem("first_name")
    localStorage.removeItem("mailerID")

    window.location.pathname = "/"
}
