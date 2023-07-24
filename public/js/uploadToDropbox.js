function uploadToDropbox() {
    try {
        fetch("https://content.dropboxapi.com/2/files/upload", {
            method: "POST",
            headers: {
                Authorization:
                    "Bearer sl.BioX3axtVgu7tlZB8rSoP9Au7SRwEvpumr_OHpnSqoOtamxwiGd_3E1qO-PYe1If5W6AsY2hfK8KQd3VH9reGi8ywB2E9DJ_Q0mtWWSBxlFyToFFOewRp_n7qKEDpjBpnm1xZ8zIbwFuVbo",
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
