async function getUsers() {
    var users = []

    await fetch("https://45.145.6.18/database/auth.php")
        .then((response) => response.json())
        .then((content) => (users = content))
        .catch((error) => console.error("Error fetching data:", error))

    return users
}
