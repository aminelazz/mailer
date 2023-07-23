async function getUsers() {
    var users = []

    await fetch("https://content.dropboxapi.com/2/files/download", {
        method: "POST",
        headers: {
            Authorization:
                "Bearer sl.BiuVuR29MaiqLmx9TIKmJxoNPqi9ed2ykmNmbmbQixaDGgOQ4rhliWaclnqOQWhD8PEeYfF6oLaHZnfEFmh3BuQq7qrHQaXEb0Yyf3OnrwuMtDGCLVhiNTCLa3IeQZetU5NhhGRwCskXdgQ",
            "Dropbox-API-Arg": '{"path":"/Auth/auth.json"}',
        },
    })
        .then((response) => {
            if (response.ok) {
                return response.text() // Get the text content of the response
            } else {
                throw new Error("Request failed with status " + response.status)
            }
        })
        .then((content) => (users = JSON.parse(content).users))
        .catch((error) => console.error("Error:", error))

    return users
}
