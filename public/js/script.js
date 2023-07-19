function changeTimeValues() {
    var servers = document.getElementById("servers").value
    var recipients = document.getElementById("recipients").value
    var pauseAfterSend = document.getElementById("pauseAfterSend").value
    var rotationAfterField = document.getElementById("rotationAfter")
    var BCCnumber = document.getElementById("BCCnumber")
    var hours = 24 * 3600

    if ((servers && recipients) != "") {
        var serversCount = servers.split("\n").length
        var recipientsCount = recipients.split("\n").length

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
    // console.log(filesName.join(", "))
}

function submitForm() {
    var recipients = document.getElementById("recipients").value.toString()
    var recipientsCount = recipients.split("\n").length
    console.log(recipients)
    console.log(recipientsCount)
    console.log(BCCnumber.value)

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
        console.log($(this))
        var formData = $(this).serialize()

        // BCCnumber.value > recipientsCount
        //     ? BCCnumber.setCustomValidity(
        //           "The 'Number of Emails In Bcc' number must be smaller than number of recipients"
        //       )
        //     : BCCnumber.setCustomValidity("")
        sendEmails(formData)
    })

    // Handle Cancel button click
    $("#cancelBtn").click(function () {
        // Set a flag to indicate that the Cancel button has been clicked
        window.cancelled = true
    })
})

function sendEmails(formData) {
    // Clear the response area
    $("#responseArea").html("")

    console.log(formData)

    // Send AJAX request to send_email.php
    try {
        $.ajax({
            type: "POST",
            url: "public/functions/send_email.php",
            data: formData,
            success: function (response) {
                // Response is an array of email responses
                console.log(response)
                $.each(response, function (recipient, message) {
                    var emailResponse = JSON.parse(response)
                    // Display the response message next to each recipient's email address
                    $("#responseArea").append(
                        // "<p>" + recipient + ": " + message + "</p>"
                        "<tr>" +
                            '<th scope="row">1</th>' +
                            "<td>" +
                            emailResponse.recipient +
                            "</td>" +
                            "<td>" +
                            emailResponse.message +
                            "</td>" +
                            "</tr>"
                    )
                })
            },
        })
    } catch (error) {
        console.log(error)
    }
}