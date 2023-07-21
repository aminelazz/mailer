var sendStatus = ""
var progressBar = document.getElementById("progressBar")
var progressBarContainer = document.getElementById("progressBarContainer")
var sendCount = document.getElementById("sendCount")

// Start index of recipients
let reachedRecipientIndex = sessionStorage.getItem("reachedRecipientIndex")
var start_index = reachedRecipientIndex ? reachedRecipientIndex : 0

var statusLabel = document.getElementById("status")

window.addEventListener("load", () => {
    var attachementsName = document.getElementById("attachementsName")
    var clearButton = document.getElementById("clearAttachements")
    var failed = document.getElementById("failed")

    statusLabel.innerText = "Status: Pending"

    // Clear the failed area
    failed.value = ""

    if (attachementsName.value != "") {
        clearButton.classList.toggle("invisible")
    }

    previewCreative()
    fileUpload()
    clearFiles()
})

function preventUnload() {
    return true
}

function changeTimeValues() {
    var servers = document.getElementById("servers").value.split("\n")
    var recipients = document.getElementById("recipients").value.split("\n")
    var pauseAfterSend = document.getElementById("pauseAfterSend").value
    var rotationAfterField = document.getElementById("rotationAfter")
    var BCCnumber = document.getElementById("BCCnumber")
    var hours = 24 * 3600

    recipients = recipients.filter((element) => element !== "")
    servers = servers.filter((element) => element !== "")

    var serversCount = servers.length
    var recipientsCount = recipients.length

    if (recipientsCount > 50) {
        if ((servers && recipients) != "") {
            // Total number of mails sent in one batch
            let mailsPerBatch = serversCount * BCCnumber.value

            // Total number of batches required
            let totalBatches = Math.ceil(recipientsCount / mailsPerBatch)

            // Total time spent sending emails
            let totalSendTime = totalBatches * pauseAfterSend

            // Rotation after (seconds)
            let rotationAfter = Math.floor((hours - totalSendTime) / 60)
            rotationAfterField.value = rotationAfter

            if (rotationAfter <= 0) {
                rotationAfterField.setCustomValidity(
                    "The 'Rotation After' field must be positive"
                )
            } else {
                rotationAfterField.setCustomValidity("")
            }
        }
    }
}

function previewCreative() {
    var creativeField = document.getElementById("creative")
    var previewField = document.getElementById("preview")
    var creative = creativeField.value

    previewField.innerHTML = creative
}

function generatePattern() {
    var AZCheck = document.getElementById("AZ").checked
    var azCheck = document.getElementById("az").checked
    var numbersCheck = document.getElementById("09").checked
    var length = document.getElementById("length").value
    var patternField = document.getElementById("pattern")

    if (!length == "") {
        if (AZCheck || azCheck || numbersCheck == true) {
            let alpha = AZCheck || azCheck ? "a" : ""
            var alphaCase = ""

            if (AZCheck && !azCheck) {
                alphaCase = "u"
            } else if (azCheck && !AZCheck) {
                alphaCase = "l"
            }
            let numbers = numbersCheck ? "n" : ""

            patternField.value = `[${alpha}${numbers}${alphaCase}_${length}]`
        } else {
            patternField.value = ""
        }
    }
}

function fileUpload() {
    var attachements = document.getElementById("attachements")
    var attachementsName = document.getElementById("attachementsName")
    var clearButton = document.getElementById("clearAttachements")

    let uploadedFiles = attachements.files
    let filesName = []

    // Put files names in the new array
    for (let i = 0; i < uploadedFiles.length; i++) {
        filesName.push(uploadedFiles[i].name)
    }

    // Add '"' to the files names
    for (let i = 0; i < filesName.length; i++) {
        filesName[i] = `"${filesName[i]}"`
    }

    // Show files names in the text input
    attachementsName.value = filesName.join(", ")

    if (attachementsName.value != "") {
        clearButton.classList.remove("invisible")
    }
}

function clearFiles() {
    let attachements = document.getElementById("attachements")
    let newFileInput = document.createElement("input")
    var clearButton = document.getElementById("clearAttachements")

    clearButton.classList.add("invisible")

    newFileInput.type = "file"
    newFileInput.id = attachements.id
    newFileInput.name = attachements.name
    newFileInput.hidden = true
    newFileInput.multiple = true
    newFileInput.addEventListener("change", fileUpload)
    newFileInput.addEventListener("input", fileUpload)

    attachements.parentNode.replaceChild(newFileInput, attachements)

    fileUpload()
}

function checkFields() {
    var servers = document.getElementById("servers")
    var recipients = document.getElementById("recipients")
    var recipientsCount = recipients.value.toString().split("\n").length

    BCCnumber.setCustomValidity("")

    if (BCCnumber.value > recipientsCount) {
        BCCnumber.setCustomValidity(
            "The 'Number of Emails In Bcc' number must be smaller than number of recipients"
        )
    }

    let serverpattern = /^(?:[\w.-]+:\d+:(?:tls|ssl):[\w.-]+@[\w.-]+:\S+)$/gm

    servers.setCustomValidity("")

    if (!serverpattern.test(servers.value.toString())) {
        servers.setCustomValidity(
            "Please enter at least one valid smtp server or multi-line smtp servers"
        )
    }

    let recipientspattern = /^[\w.-]+@[\w.-]+$/gm

    recipients.setCustomValidity("")

    if (!recipientspattern.test(recipients.value.toString())) {
        recipients.setCustomValidity(
            "Please enter at least one valid email address or multi-line email address"
        )
    }
}

function startSend() {
    sendStatus = "sending"
    statusLabel.innerText = `Status: Sending`
    sendEmails()
    controlButtons()
}

function pauseSend() {
    sendStatus = "paused"
    statusLabel.innerText = `Status: Paused`
    controlButtons()
}
function stopSend() {
    sendStatus = "stopped"
    statusLabel.innerText = `Status: Stopped`
    controlButtons()
}

function controlButtons() {
    var start       = document.getElementById("start") // prettier-ignore
    var controlArea = document.getElementById("controlArea") // prettier-ignore
    let play        = document.getElementById("play") // prettier-ignore
    let pause       = document.getElementById("pause") // prettier-ignore
    let stop        = document.getElementById("stop") // prettier-ignore

    switch (sendStatus) {
        case "sending":
            controlArea.classList.remove("invisible")
            start.disabled = true
            play.disabled = true
            pause.disabled = false
            progressBar.classList.remove(
                "bg-play",
                "bg-success",
                "bg-warning",
                "bg-danger"
            )
            progressBar.classList.add("bg-play")
            break

        case "paused":
            controlArea.classList.remove("invisible")
            start.disabled = true
            play.disabled = false
            pause.disabled = true
            progressBar.classList.remove(
                "bg-play",
                "bg-success",
                "bg-warning",
                "bg-danger"
            )
            progressBar.classList.add("bg-warning")
            break

        case "stopped":
            controlArea.classList.add("invisible")
            start.disabled = false
            play.disabled = false
            pause.disabled = false
            stop.disabled = false
            progressBar.classList.remove(
                "bg-play",
                "bg-success",
                "bg-warning",
                "bg-danger"
            )
            progressBar.classList.add("bg-danger")
            break

        case "completed":
            controlArea.classList.add("invisible")
            start.disabled = false
            play.disabled = false
            pause.disabled = false
            stop.disabled = false
            progressBar.classList.remove(
                "bg-play",
                "bg-success",
                "bg-warning",
                "bg-danger"
            )
            progressBar.classList.add("success")
            statusLabel.innerText = `Status: Completed`
            break

        default:
            break
    }
}

// Handle form submit
$(document).ready(function () {
    // Handle form submission
    $("#sendForm").submit(function (event) {
        event.preventDefault()

        // Scroll to the Result Section
        const resultSection = document.getElementById("Result")
        resultSection.scrollIntoView({ behavior: "smooth" })

        // Clear the failed area
        var failed = document.getElementById("failed")
        failed.value = ""

        // Set sendStatus to "sending" and clear sessionStorage
        sendStatus = "sending"
        statusLabel.innerText = `Status: Sending`
        sessionStorage.clear()

        // Clear the response area
        $("#responseArea").html("")

        progressBar.innerText = "0"
        progressBar.style.width = "0"
        sendCount.innerText = "0 / 0"
        progressBarContainer.classList.remove("invisible")

        // Control buttons
        controlButtons()

        start_index = 0

        // Send email
        sendEmails()
    })
})

async function sendEmails() {
    var fields = {
        servers             : document.getElementById("servers"), // prettier-ignore
        pauseAfterSend      : document.getElementById("pauseAfterSend"), // prettier-ignore
        rotationAfter       : document.getElementById("rotationAfter"), // prettier-ignore
        BCCnumber           : document.getElementById("BCCnumber"), // prettier-ignore
        headers             : document.getElementById("headers"), // prettier-ignore
        contentType         : document.getElementById("contentType"), // prettier-ignore
        charset             : document.getElementById("charset"), // prettier-ignore
        encoding            : document.getElementById("encoding"), // prettier-ignore
        priority            : document.getElementById("priority"), // prettier-ignore
        fromNameEncoding    : document.getElementById("fromNameEncoding"), // prettier-ignore
        fromName            : document.getElementById("fromName"), // prettier-ignore
        subjectEncoding     : document.getElementById("subjectEncoding"), // prettier-ignore
        subject             : document.getElementById("subject"), // prettier-ignore
        fromEmailCheck      : document.getElementById("fromEmailCheck"), // prettier-ignore
        fromEmail           : document.getElementById("fromEmail"), // prettier-ignore
        replyToCheck        : document.getElementById("replyToCheck"), // prettier-ignore
        replyTo             : document.getElementById("replyTo"), // prettier-ignore
        returnPathCheck     : document.getElementById("returnPathCheck"), // prettier-ignore
        returnPath          : document.getElementById("returnPath"), // prettier-ignore
        link                : document.getElementById("link"), // prettier-ignore
        attachements        : document.getElementById("attachements"), // prettier-ignore
        creative            : document.getElementById("creative"), // prettier-ignore
        recipients          : document.getElementById("recipients"), // prettier-ignore
        blacklist           : document.getElementById("blacklist"), // prettier-ignore
        failed              : document.getElementById("failed"), // prettier-ignore
    }

    var servers     = fields.servers.value.split("\n") // prettier-ignore
    var recipients  = fields.recipients.value.split("\n") // prettier-ignore
    var blacklist   = fields.blacklist.value.split("\n") // prettier-ignore

    const pauseAfterSend    = fields.pauseAfterSend.value * 1000 // prettier-ignore
    const rotationAfter     = fields.rotationAfter.value * 1000 // prettier-ignore
    const BCCnumber         = fields.BCCnumber.value // prettier-ignore
    const headers           = fields.headers.value // prettier-ignore
    const contentType       = fields.contentType.value // prettier-ignore
    const charset           = fields.charset.value // prettier-ignore
    const encoding          = fields.encoding.value // prettier-ignore
    const priority          = fields.priority.value // prettier-ignore
    const fromNameEncoding  = fields.fromNameEncoding.value // prettier-ignore
    const fromName          = fields.fromName.value // prettier-ignore
    const subjectEncoding   = fields.subjectEncoding.value // prettier-ignore
    const subject           = fields.subject.value // prettier-ignore
    const fromEmailCheck    = fields.fromEmailCheck.checked // prettier-ignore
    const fromEmail         = fields.fromEmail.value // prettier-ignore
    const replyToCheck      = fields.replyToCheck.checked // prettier-ignore
    const replyTo           = fields.replyTo.value // prettier-ignore
    const returnPathCheck   = fields.returnPathCheck.checked // prettier-ignore
    const returnPath        = fields.returnPath.value // prettier-ignore
    const link              = fields.link.value // prettier-ignore
    const attachements      = fields.attachements.files // prettier-ignore
    const creative          = fields.creative.value // prettier-ignore

    // Remove empty lines
    servers     = servers.filter((element) => element !== "") // prettier-ignore
    recipients  = recipients.filter((element) => element !== "") // prettier-ignore
    blacklist   = blacklist.filter((element) => element !== "") // prettier-ignore

    // Remove recipients who are in the blacklist
    const filteredRecipients = recipients.filter(
        (recipient) => !blacklist.includes(recipient)
    )

    console.log(filteredRecipients)

    // Calculate rotations number
    const sendPerRotation = BCCnumber * servers.length
    const result = filteredRecipients.length / sendPerRotation
    const nbrRotations =
        result !== Math.floor(result) ? Math.ceil(result) : result

    // Perform the rotation
    rotation: for (let i = 0; i < nbrRotations; i++) {
        var serverCount
        if (servers.length >= filteredRecipients.length) {
            if (BCCnumber == filteredRecipients.length) {
                serverCount = 1
            } else {
                serverCount = filteredRecipients.length - BCCnumber + 1
            }
        } else {
            serverCount = servers.length
        }

        // Loop through the servers
        for (let j = 0; j < serverCount; j++) {
            let server = servers[j]

            // prettier-ignore
            // Loop through the filteredRecipients and send emails
            for (let k = start_index; k < start_index + parseInt(BCCnumber); k++) {

                // Stop the loops after finishing all recipients
                if (k > (filteredRecipients.length - 1)) {
                    break rotation
                }

                switch (sendStatus) {
                    case "paused":
                        sessionStorage.setItem("reachedRecipientIndex", k)
                        break rotation
                        break;

                    case "stopped":
                        break rotation
                        break;
                
                    default:
                        break;
                }              

                let recipient = filteredRecipients[k]
                console.log(`Recipient: ${recipient}`)

                let formData = new FormData()
                formData.append("server", server)
                formData.append("headers", headers)
                formData.append("contentType", contentType)
                formData.append("charset", charset)
                formData.append("encoding", encoding)
                formData.append("priority", priority)
                formData.append("fromNameEncoding", fromNameEncoding)
                formData.append("fromName", fromName)
                formData.append("subjectEncoding", subjectEncoding)
                formData.append("subject", subject)
                formData.append("fromEmailCheck", fromEmailCheck)
                formData.append("fromEmail", fromEmail)
                formData.append("replyToCheck", replyToCheck)
                formData.append("replyTo", replyTo)
                formData.append("returnPathCheck", returnPathCheck)
                formData.append("returnPath", returnPath)
                formData.append("link", link)
                formData.append("creative", creative)
                formData.append("recipient", recipient)

                for (let l = 0; l < attachements.length; l++) {
                    formData.append("attachments[]", attachements[l])
                }

                const now = new Date()

                // Get the current hours, minutes, and seconds
                const currentHours = now.getHours()
                const currentMinutes = now.getMinutes()
                const currentSeconds = now.getSeconds()

                // Formatting single-digit values with leading zeros (optional)
                const formattedHours = currentHours.toString().padStart(2, "0")
                const formattedMinutes = currentMinutes
                    .toString()
                    .padStart(2, "0")
                const formattedSeconds = currentSeconds
                    .toString()
                    .padStart(2, "0")

                // Send AJAX request to send_email.php
                try {
                    /*await */$.ajax({
                        type: "POST",
                        url: "public/functions/send_email.php",
                        data: formData,
                        contentType: false, // Set contentType to false, as FormData already sets it to 'multipart/form-data'
                        processData: false, // Set processData to false, as FormData already processes the data
                        success: function (responses) {
                            // responses is an a group of json objects delimited by '||'
                            // remove the last '||'
                            responses = responses.slice(0, -2)
                            // Split responses to an array containing json objects
                            var responsesArray = responses.split("||")
                            // Iterate over the array to get the key and the value
                            $.each(responsesArray, function (key, value) {
                                // Convert the json object to a js object
                                // Convert it again to an array which gives 2 nested arrays
                                // Get the first nested array which contains the email and its response
                                var emailResponse = Object.entries(
                                    JSON.parse(value)
                                )[0]

                                // Get the second value of the second nested array which contains the status (color of text in response using the bootstrap class)
                                var status = Object.entries(
                                    JSON.parse(value)
                                )[1][1]

                                // prettier-ignore
                                // Display the response message next to each recipient's email address
                                $("#responseArea").append(
                                    `<tr>
                                        <th scope="row" style="width: 10%;">
                                            ${k + 1} / ${filteredRecipients.length}
                                        </th>
                                        <td class="text-center" style="width: 40%;">
                                            ${emailResponse[0]}
                                        </td>
                                        <td class="text-${status}">
                                            <div class="text-center response">
                                                ${emailResponse[1]}
                                            <div>
                                        </td>
                                        <td class="text-center" style="width: 10%;">
                                            ${formattedHours}:${formattedMinutes}:${formattedSeconds}
                                        </td>
                                    </tr>`
                                )

                                // Fill the Bounced area with failed sends
                                if (status == "danger") {
                                    fields.failed.value += `${emailResponse[0]}\n`
                                }

                                // Configure the progress bar
                                var percentage = ((k + 1) / filteredRecipients.length) * 100

                                progressBar.innerText = `${percentage.toFixed(0)}%`
                                progressBar.style.width = `${percentage.toFixed(0)}%`

                                sendCount.innerText = `${k + 1} / ${filteredRecipients.length}`
                            })
                        },
                    })
                } catch (error) {
                    console.log(error)
                }

                // Set the sendStatus value to completed when reaching last recipient and set buttons properties
                if (k == (filteredRecipients.length - 1)) {
                    setTimeout(() => {
                        sendStatus = "completed"
                        controlButtons()
                    },1000)
                }

                await delay(100)
            }
            start_index += parseInt(BCCnumber)
            await delay(pauseAfterSend)
        }
        if (servers.length > 1) {
            await delay(rotationAfter)
        }
    }
}

function delay(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms))
}
