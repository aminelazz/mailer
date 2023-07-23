// Check auth
const keyExists = localStorage.getItem("connected") !== null
if (!keyExists && window.location.pathname == "/send.php") {
    window.location.pathname = "/"
    alert("You are not logged in, log in first.")
    console.log("not logged in")
} else if (keyExists && window.location.pathname == "/") {
    window.location.pathname = "/send.php"
    console.log("logged in")
}

console.log(window.location.pathname)

async function login(event) {
    event.preventDefault()
    const usernameInput = document.getElementById("username")
    const passwordInput = document.getElementById("password")
    const feedback = document.getElementById("feedback")

    usernameInput.classList.remove("is-invalid")
    passwordInput.classList.remove("is-invalid")

    const username = usernameInput.value
    const password = passwordInput.value

    const users = await getUsers()

    const matchedUser = users.find(
        (user) => user.name === username && user.pass === password
    )

    if (matchedUser) {
        // Save to local storage and redirect back home page
        localStorage.setItem("connected", true)
        localStorage.setItem("username", username)

        usernameInput.classList.remove("is-invalid")
        usernameInput.classList.add("is-valid")
        passwordInput.classList.remove("is-invalid")
        passwordInput.classList.add("is-valid")

        window.location.pathname += "send.php"
    } else {
        usernameInput.classList.remove("is-valid")
        usernameInput.classList.add("is-invalid")
        passwordInput.classList.remove("is-valid")
        passwordInput.classList.add("is-invalid")
    }
}
