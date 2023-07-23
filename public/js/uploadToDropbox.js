function uploadToDropbox() {
    try {
        fetch("https://content.dropboxapi.com/2/files/upload", {
            method: "POST",
            headers: {
                Authorization:
                    "Bearer sl.BiuVuR29MaiqLmx9TIKmJxoNPqi9ed2ykmNmbmbQixaDGgOQ4rhliWaclnqOQWhD8PEeYfF6oLaHZnfEFmh3BuQq7qrHQaXEb0Yyf3OnrwuMtDGCLVhiNTCLa3IeQZetU5NhhGRwCskXdgQ",
                "Dropbox-API-Arg":
                    '{"autorename":false,"mode":"add","mute":false,"path":"/History/test.txt","strict_conflict":false}',
                "Content-Type": "application/octet-stream",
            },
            body: new File(["fd"], "History/test.txt"),
        })
        console.log("File uploaded successfully")
    } catch (error) {
        console.log("Error: " + error)
    }
}
