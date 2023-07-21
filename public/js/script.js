window.addEventListener("load", () => {
    var attachementsName = document.getElementById("attachementsName")
    var clearButton = document.getElementById("clearAttachements")

    if (attachementsName.value != "") {
        clearButton.classList.toggle("invisible")
    }

    previewCreative()
    fileUpload()
})

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
    var recipients = document.getElementById("recipients").value.toString()
    var recipientsCount = recipients.split("\n").length

    BCCnumber.setCustomValidity("")

    if (BCCnumber.value > recipientsCount) {
        BCCnumber.setCustomValidity(
            "The 'Number of Emails In Bcc' number must be smaller than number of recipients"
        )
    }
}

// Handle form submit
$(document).ready(function () {
    // Handle form submission
    $("#sendForm").submit(function (event) {
        event.preventDefault()

        sendEmails()
    })

    // Handle Cancel button click
    $("#cancelBtn").click(function () {
        // Set a flag to indicate that the Cancel button has been clicked
        window.cancelled = true
    })
})

async function sendEmails() {
    // Clear the response area
    $("#responseArea").html("")

    var fields = {
        servers            : document.getElementById("servers"), // prettier-ignore
        pauseAfterSend     : document.getElementById("pauseAfterSend"), // prettier-ignore
        rotationAfter      : document.getElementById("rotationAfter"), // prettier-ignore
        BCCnumber          : document.getElementById("BCCnumber"), // prettier-ignore
        headers            : document.getElementById("headers"), // prettier-ignore
        contentType        : document.getElementById("contentType"), // prettier-ignore
        charset            : document.getElementById("charset"), // prettier-ignore
        encoding           : document.getElementById("encoding"), // prettier-ignore
        priority           : document.getElementById("priority"), // prettier-ignore
        fromNameEncoding   : document.getElementById("fromNameEncoding"), // prettier-ignore
        fromName           : document.getElementById("fromName"), // prettier-ignore
        subjectEncoding    : document.getElementById("subjectEncoding"), // prettier-ignore
        subject            : document.getElementById("subject"), // prettier-ignore
        fromEmailCheck     : document.getElementById("fromEmailCheck"), // prettier-ignore
        fromEmail          : document.getElementById("fromEmail"), // prettier-ignore
        replyToCheck       : document.getElementById("replyToCheck"), // prettier-ignore
        replyTo            : document.getElementById("replyTo"), // prettier-ignore
        returnPathCheck    : document.getElementById("returnPathCheck"), // prettier-ignore
        returnPath         : document.getElementById("returnPath"), // prettier-ignore
        link               : document.getElementById("link"), // prettier-ignore
        attachements       : document.getElementById("attachements"), // prettier-ignore
        creative           : document.getElementById("creative"), // prettier-ignore
        recipients         : document.getElementById("recipients"), // prettier-ignore
        blacklist          : document.getElementById("blacklist"), // prettier-ignore
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
    servers = servers.filter((element) => element !== "")
    //                                                      ↑
    //                                         Array value to delete

    recipients = recipients.filter((element) => element !== "")
    //                                                            ↑
    //                                               Array value to delete

    blacklist = blacklist.filter((element) => element !== "")
    //                                                          ↑
    //                                             Array value to delete

    // Remove recipients who are in the blacklist
    const filteredRecipients = recipients.filter(
        (recipient) => !blacklist.includes(recipient)
    )

    console.log(filteredRecipients)

    // Start index of recipients
    var start_index = 0

    // Calculate rotations number
    const sendPerRotation = BCCnumber * servers.length
    const result = filteredRecipients.length / sendPerRotation
    const nbrRotations =
        result !== Math.floor(result) ? Math.ceil(result) : result

    // Perform the rotation
    rotation: for (let i = 0; i < nbrRotations; i++) {
        // Loop through the servers
        per_server: for (let j = 0; j < servers.length; j++) {
            let server = servers[j]

            // prettier-ignore
            // Loop through the filteredRecipients and send emails
            per_recipient: for (let k = start_index; k < start_index + parseInt(BCCnumber); k++) {

                if (k > recipients.length) {
                    break
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
                    $.ajax({
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
                            console.log(responsesArray)
                            // Iterate over the array to get the kay and the value
                            $.each(
                                responsesArray,
                                function (key, value) {
                                    // Convert the json object to a js object
                                    // Convert it again to an array which gives 2 nested arrays
                                    // Get the nested array
                                    var emailResponse = Object.entries(
                                        JSON.parse(value)
                                    )[0]
                                    // prettier-ignore
                                    // Display the response message next to each recipient's email address
                                    $("#responseArea").append(
                                        `<tr>
                                            <th scope="row" style="width: 2%;">
                                                ${k + 1}
                                            </th>
                                            <td class="text-center">
                                                ${emailResponse[0]}
                                            </td>
                                            <td class="text-center" style="width: 10%;">
                                                ${formattedHours}:${formattedMinutes}:${formattedSeconds}
                                            </td>
                                            <td class="response">
                                                    ${
                                                        emailResponse[1]
                                                    } jjjjjjjjjjjjjjjjjkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk
                                            </td>
                                        </tr>`
                                    )
                                }
                            )
                        },
                    })
                } catch (error) {
                    console.log(error)
                }
            }
            start_index += parseInt(BCCnumber)
            await delay(pauseAfterSend)
        }
        await delay(rotationAfter)
    }
}

function delay(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms))
}
