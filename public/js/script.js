window.addEventListener("message", receiveMessage, false)

async function receiveMessage(event) {
    let offerData = event.data
    console.log(offerData)

    // Update the fields in the parent page based on the received data
    document.getElementById("servers").innerHTML = offerData.servers.replaceAll(",", "\n") // prettier-ignore
    document.getElementById("headers").innerHTML = offerData.header.replaceAll("||", "\n") // prettier-ignore
    document.getElementById("contentType").value = offerData.contentType // prettier-ignore
    document.getElementById("charset").value = offerData.charset // prettier-ignore
    document.getElementById("encoding").value = offerData.encoding // prettier-ignore
    document.getElementById("priority").value = offerData.priority // prettier-ignore
    document.getElementById("offerID").value = offerData.offerID // prettier-ignore
    document.getElementById("offerName").value = offerData.offerName // prettier-ignore
    document.getElementById("country").value = offerData.countryID // prettier-ignore
    document.getElementById("fromNameEncoding").value = offerData.fromNameEncoding // prettier-ignore
    document.getElementById("subjectEncoding").value = offerData.subjectEncoding // prettier-ignore
    document.getElementById("fromEmailCheck").checked = !!+offerData.fromEmailCheck // prettier-ignore
    document.getElementById("fromEmail").value = offerData.fromEmail // prettier-ignore
    document.getElementById("replyToCheck").checked = !!+offerData.replyToCheck // prettier-ignore
    document.getElementById("replyTo").value = offerData.replyTo // prettier-ignore
    document.getElementById("returnPathCheck").checked = !!+offerData.returnPathCheck // prettier-ignore
    document.getElementById("returnPath").value = offerData.returnPath // prettier-ignore
    document.getElementById("link").value = offerData.link // prettier-ignore
    document.getElementById("recipients").innerHTML = offerData.recipients.replaceAll(",", "\n") // prettier-ignore
    document.getElementById("blacklist").innerHTML = offerData.blacklist.replaceAll(",", "\n") // prettier-ignore

    let fromNamesField = document.getElementsByClassName("fromNames")[0] // prettier-ignore
    let subjectsField = document.getElementsByClassName("subjects")[0] // prettier-ignore
    let creativeFields = document.getElementsByClassName("creative") // prettier-ignore

    let fromNames = offerData.fromName.split("||")
    let subjects = offerData.subject.split("||")
    let creatives = offerData.creative.split("||||")

    // From Names
    fromNamesField.innerHTML = ""

    for (let i = 0; i < fromNames.length; i++) {
        const token = document.createElement("div")
        token.classList.add("token")

        const content = document.createElement("span")
        content.classList.add("fromName")

        content.textContent = fromNames[i]

        const deleteButton = document.createElement("button")
        deleteButton.classList.add("tokenButton")
        deleteButton.textContent = "x"
        deleteButton.addEventListener("click", () => {
            token.remove()
        })

        token.appendChild(content)
        token.appendChild(deleteButton)
        fromNamesField.appendChild(token)
    }

    // Subjects
    subjectsField.innerHTML = ""

    for (let i = 0; i < subjects.length; i++) {
        const token = document.createElement("div")
        token.classList.add("token")

        const content = document.createElement("span")
        content.classList.add("subject")

        content.textContent = subjects[i]

        const deleteButton = document.createElement("button")
        deleteButton.classList.add("tokenButton")
        deleteButton.textContent = "x"
        deleteButton.addEventListener("click", () => {
            token.remove()
        })

        token.appendChild(content)
        token.appendChild(deleteButton)
        subjectsField.appendChild(token)
    }

    // Creatives
    for (let i = 0; i < creativeFields.length; i++) {
        if (i != 0) {
            creativeFields[i].parentNode.parentNode.remove()
        }
    }
    for (let i = 0; i < creatives.length; i++) {
        if (creativeFields.length < creatives.length) {
            await addCreative()
        }
        $(".creative").eq(i).summernote("code", creatives[i])
        // previewCreative(creativeFields[i])
    }

    previewCreative()
    organizeBlacklist(event)
    configureRecipientsBlacklist()

    // Go to the send tab
    document.getElementById("nav-send-tab").click()
}

var sendStatus = ""
var progressBar = document.getElementById("progressBar")
var progressBarContainer = document.getElementById("progressBarContainer")
var sendCount = document.getElementById("sendCount")
var refreshDiv = document.getElementById("refreshDiv")
var test = false
var lastRecipientCount = 0

try {
    var recipientsField = document.getElementById("recipients")
    var blacklistField = document.getElementById("blacklist")

    recipientsField.addEventListener("input", () => {
        configureRecipientsBlacklist()
    })

    blacklistField.addEventListener("input", () => {
        configureRecipientsBlacklist()
    })

    let mailerName = document.getElementById("mailerName")
    mailerName.innerText = localStorage.getItem("first_name")

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

        const summernotes = document.querySelectorAll(".note-editor, .note-frame")
        summernotes.forEach((summernote) => {
            summernote.style.padding = "0"
        })

        previewCreative()
        fileUpload()
        clearFiles()
        configureRecipientsBlacklist()
    })
} catch (error) {}

// Start index of recipients
var start_index = 0
var counter = 0

var statusLabel = document.getElementById("status")

function preventUnload() {
    return true
}

// function changeTimeValues() {
//     var servers = document.getElementById("servers").value.split("\n")
//     var recipients = document.getElementById("recipients").value.split("\n")
//     var pauseAfterSend = document.getElementById("pauseAfterSend").value
//     var rotationAfterField = document.getElementById("rotationAfter")
//     var BCCnumber = document.getElementById("BCCnumber")
//     var hours = 24 * 3600

//     recipients = recipients.filter((element) => element !== "")
//     servers = servers.filter((element) => element !== "")

//     var serversCount = servers.length
//     var recipientsCount = recipients.length

//     if (recipientsCount > 50) {
//         if ((servers && recipients) != "") {
//             // Total number of mails sent in one batch
//             let mailsPerBatch = serversCount * parseInt(BCCnumber.value)

//             // Total number of batches required
//             let totalBatches = Math.ceil(recipientsCount / mailsPerBatch)

//             // Total time spent sending emails
//             let totalSendTime = totalBatches * pauseAfterSend

//             // Rotation after (seconds)
//             let rotationAfter = Math.floor((hours - totalSendTime) / 60)
//             rotationAfterField.value = rotationAfter

//             if (rotationAfterField.value <= 0) {
//                 rotationAfterField.setCustomValidity("The 'Rotation After' field must be positive")
//             } else {
//                 rotationAfterField.setCustomValidity("")
//             }
//         }
//     }
// }

function previewCreative(event) {
    try {
        var creativeField = event
        var previewField = event.parentNode.parentNode.querySelector(".preview")
        var creative = creativeField.value

        previewField.innerHTML = creative
    } catch (error) {}
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
    let servers = document.getElementById("servers")
    let recipients = document.getElementById("recipients")
    let rotationAfterField = document.getElementById("rotationAfter")
    let testAfter = document.getElementById("testAfter")
    let emailTest = document.getElementById("emailTest")
    let start = document.getElementById("start")
    let count = document.getElementById("count")
    let fromName = document.getElementById("fromName")
    let fromNames = document.getElementsByClassName("fromNames")
    let subject = document.getElementById("subject")
    let subjects = document.getElementsByClassName("subjects")

    let recipientsArray = recipients.value.toString().split("\n")
    recipientsArray = recipientsArray.filter((element) => element !== "")
    let recipientsCount = recipientsArray.length

    let emailTestArray = emailTest.value.toString().split("; ")
    emailTestArray = emailTestArray.filter((element) => element !== "")
    let emailTestCount = emailTestArray.length

    let allRecipientsCount = recipientsCount + emailTestCount

    // BCC number Validity
    BCCnumber.setCustomValidity("")

    if (recipients.value != "" && emailTest.value != "") {
        if (parseInt(BCCnumber.value) > parseInt(allRecipientsCount)) {
            BCCnumber.setCustomValidity("The 'Number of Emails In Bcc' number must be smaller than number of recipients and test email addresses")
        }
    } else if (emailTest.value != "") {
        if (parseInt(BCCnumber.value) > emailTestCount) {
            BCCnumber.setCustomValidity("The 'Number of Emails In Bcc' number must be smaller than number of test email addresses")
        }
    } else if (recipients.value != "") {
        if (parseInt(BCCnumber.value) > recipientsCount) {
            BCCnumber.setCustomValidity("The 'Number of Emails In Bcc' number must be smaller than number of recipients")
        }
    }

    // Rotation After Validity
    if (parseInt(rotationAfterField.value) <= 0) {
        rotationAfterField.setCustomValidity("The 'Rotation After' field must be positive")
    } else {
        rotationAfterField.setCustomValidity("")
    }

    // servers validity
    let serverpattern = /^(?:[\w.-]+:\d+:(?:tls|ssl):[\w.-]+@[\w.-]+:\S+)$/gim

    servers.setCustomValidity("")

    if (!serverpattern.test(servers.value.toString())) {
        servers.setCustomValidity("Please enter at least one valid smtp server or multi-line smtp servers")
    }

    // recipients & email test validity
    let recipientspattern = /^[\w.-]+@[\w.-]+$/gim

    recipients.setCustomValidity("")
    emailTest.setCustomValidity("")

    if (emailTest.value == "") {
        if (!recipientspattern.test(recipients.value.toString())) {
            recipients.setCustomValidity("Please enter at least one valid email address or multi-line email address")
            emailTest.setCustomValidity("Please enter at least one valid email address for test")
        }
    }

    // fromNames & subjects validity
    fromName.setCustomValidity("")
    subject.setCustomValidity("")

    if (fromNames[0].innerText == "") {
        fromName.setCustomValidity("Please enter at least one From Name and press Enter")
    }

    if (subjects[0].innerText == "") {
        subject.setCustomValidity("Please enter at least one Subject and press Enter")
    }

    // test after validity
    testAfter.setCustomValidity("")
    testAfter.required = false

    if (parseInt(emailTest.value.length) !== 0 && parseInt(recipients.value.length) !== 0 && parseInt(testAfter.value.length) === 0) {
        testAfter.required = true
    }

    if (emailTest.value != "" && recipients.value != "") {
        if (parseInt(testAfter.value) >= parseInt(count.value)) {
            testAfter.setCustomValidity(`Number should be lesser than Count`)
        } else {
            testAfter.setCustomValidity("")
        }
    }

    // Set custom validity to Start and Count (recipients)
    start.setCustomValidity("")
    count.setCustomValidity("")

    if (recipientsCount != 0) {
        if (start.value == "") {
            start.setCustomValidity("Please enter a start index")
        } else if (count.value == "") {
            count.setCustomValidity("Please enter a valid number of recipients")
        }
        if (parseInt(start.value) >= parseInt(recipientsCount)) {
            start.setCustomValidity("Please enter a start index smaller than number of recipients")
        }
    }
}

function configureRecipientsBlacklist() {
    let recipientsField = document.getElementById("recipients")
    let recipientsArray = recipientsField.value.toString().split("\n")
    recipientsArray = recipientsArray.filter((element) => element !== "")

    let blacklistField  = document.getElementById("blacklist") // prettier-ignore
    let blacklistNodes  = blacklistField.childNodes // prettier-ignore
    let blacklist = []

    blacklistNodes.forEach((node) => {
        blacklist.push(node.textContent.replace("<br>", ""))
    })

    blacklist = blacklist.filter((element) => element !== "") // prettier-ignore

    let count = document.getElementById("count")

    const filteredRecipients = recipientsArray.filter((recipient) => !blacklist.includes(recipient))
    const blacklistedRecipients = recipientsArray.filter((recipient) => blacklist.includes(recipient))

    let blacklistList = document.getElementById("blacklistList")
    blacklistList.value = blacklistedRecipients.join("\n")

    blacklistNodes.forEach((node) => {
        let nodeContent = node.textContent.replace("<br>", "")
        if (blacklistedRecipients.includes(nodeContent)) {
            blacklistField.className = "form-control w-100"
            node.className = ""

            if (node.nodeType == 3) {
                blacklistField.classList.add("text-danger")
                blacklistField.classList.add("fw-semibold")
            } else {
                node.classList.add("text-danger")
                node.classList.add("fw-semibold")
            }
        } else {
            blacklistField.className = "form-control w-100"
            node.className = ""
        }
    })

    let nbrRecipients = document.getElementById("nbrRecipients")
    let nbrBlacklist = document.getElementById("nbrBlacklist")

    nbrRecipients.textContent = filteredRecipients.length
    count.value = filteredRecipients.length == "0" ? "" : filteredRecipients.length

    const blacklisted = recipientsArray.length - filteredRecipients.length
    nbrBlacklist.textContent = blacklisted
}

function organizeBlacklist(event) {
    event.preventDefault()

    let blacklistField  = document.getElementById("blacklist") // prettier-ignore
    let blacklistNodes  = blacklistField.childNodes // prettier-ignore

    let blacklistSpinner = document.getElementById("blacklistSpinner")
    blacklistSpinner.classList.remove("invisible")

    // Get the pasted text
    let pastedText
    try {
        pastedText = (event.clipboardData || window.clipboardData).getData("text")
    } catch (error) {
        pastedText = blacklistField.innerHTML
    }

    // Append pasted text to the div
    blacklistField.innerHTML += pastedText.replaceAll("\n", "<br>")

    // Create a new div element for each text node and move the content to it
    const divsToAdd = []
    for (let i = 0; i < blacklistNodes.length; i++) {
        const node = blacklistNodes[i]
        if (node.nodeType === Node.TEXT_NODE) {
            const div = document.createElement("div")
            div.textContent = node.textContent.trim()
            divsToAdd.push(div)
        }
    }

    // Insert the new divs before each <br> element
    for (let i = 0; i < divsToAdd.length; i++) {
        blacklistField.appendChild(divsToAdd[i])
    }

    // Remove the original text nodes and <br> elements
    for (let i = 0; i < blacklistNodes.length; i++) {
        const node = blacklistNodes[i]
        if (node.nodeType === Node.TEXT_NODE) {
            blacklistField.removeChild(node)
        }
    }

    const brElements = blacklistField.querySelectorAll("br")
    for (let i = 0; i < brElements.length; i++) {
        const br = brElements[i]
        br.remove()
    }

    configureRecipientsBlacklist()
    blacklistSpinner.classList.add("invisible")
}

function send() {
    sendStatus = "sending"
    statusLabel.innerText = `Status: Sending`
    sendEmails()
    controlButtons()
}

function pauseSend() {
    sendStatus = "paused"
    statusLabel.innerText = `Status: Paused`
    controlButtons()
    sessionStorage.setItem("paused", true)
}
function stopSend() {
    sendStatus = "stopped"
    statusLabel.innerText = `Status: Stopped`
    controlButtons()
    sessionStorage.setItem("paused", false)
}

function controlButtons() {
    var start       = document.getElementById("startSend") // prettier-ignore
    var controlArea = document.getElementById("controlArea") // prettier-ignore
    let play        = document.getElementById("play") // prettier-ignore
    let pause       = document.getElementById("pause") // prettier-ignore
    let stop        = document.getElementById("stop") // prettier-ignore
    let test        = document.getElementById("test") // prettier-ignore

    switch (sendStatus) {
        case "sending":
            controlArea.classList.remove("invisible")
            start.disabled = true
            play.disabled = true
            pause.disabled = false
            test.disabled = false
            progressBar.classList.remove("bg-play", "bg-success", "bg-warning", "bg-danger")
            progressBar.classList.add("bg-play")
            break

        case "paused":
            controlArea.classList.remove("invisible")
            start.disabled = true
            play.disabled = false
            pause.disabled = true
            test.disabled = true
            progressBar.classList.remove("bg-play", "bg-success", "bg-warning", "bg-danger")
            progressBar.classList.add("bg-warning")
            break

        case "stopped":
            controlArea.classList.add("invisible")
            setTimeout(() => {
                start.disabled = false
            }, 2000)
            play.disabled = false
            pause.disabled = false
            stop.disabled = false
            test.disabled = true
            progressBar.classList.remove("bg-play", "bg-success", "bg-warning", "bg-danger")
            progressBar.classList.add("bg-danger")
            break

        case "completed":
            controlArea.classList.add("invisible")
            setTimeout(() => {
                start.disabled = false
            }, 2000)
            play.disabled = false
            pause.disabled = false
            stop.disabled = false
            test.disabled = true
            progressBar.classList.remove("bg-play", "bg-success", "bg-warning", "bg-danger")
            progressBar.classList.add("success")
            statusLabel.innerText = `Status: Completed`
            break

        default:
            break
    }
}

function showRefresh() {
    refreshDiv.classList.remove("invisible")
}

function hideRefresh() {
    refreshDiv.classList.add("invisible")
}

function refreshIframe() {
    let dropboxIframe = document.getElementById("dropboxFolder")
    dropboxIframe.src = dropboxIframe.src
}

function addToken(event) {
    console.log(event)

    if (event.key === "Enter") {
        event.preventDefault()
        const directText = event.target.value
        if (directText.trim() !== "") {
            let tokenContainer = document.getElementsByClassName(`${event.target.id}s`)[0]

            let splitedDirectText = directText.split("; ")
            splitedDirectText = splitedDirectText.filter((element) => element !== "")
            splitedDirectText.forEach((element) => {
                const token = document.createElement("div")
                token.classList.add("token")

                const content = document.createElement("span")
                content.classList.add(event.target.id)
                content.textContent = element

                const deleteButton = document.createElement("button")
                deleteButton.classList.add("tokenButton")
                deleteButton.type = "button"
                deleteButton.textContent = "x"
                deleteButton.addEventListener("click", () => {
                    token.remove()
                })

                token.appendChild(content)
                token.appendChild(deleteButton)
                tokenContainer.appendChild(token)
            })
            event.target.value = "" // Clear the div after creating the token
        }
    }

    // As a last resort
    return false
}

function getCreatives() {
    let creatives = document.getElementsByClassName("creative")
    let creativeArray = []

    for (let i = 0; i < creatives.length; i++) {
        creativeArray.push($(".creative").eq(i).summernote("code"))
    }
    // console.log(creativeArray)
    return creativeArray
}

async function addCreative() {
    let creativeContainer = document.getElementById("creativeContainer")
    let textareas = document.getElementsByClassName("textareas")[0]

    let creativeNew = textareas.cloneNode(true)
    creativeNew.classList.add("mt-2")

    // Clear text in the textarea and div elements within the duplicated container
    const creativeInCreativeNw = creativeNew.getElementsByTagName("textarea")
    const divInCreativeNw = creativeNew.querySelectorAll("div>div")[0]
    const previewInCreativeNw = creativeNew.querySelectorAll(".preview")
    const removeButtonInCreativeNw = creativeNew.querySelectorAll(".removeCreative")[0]
    const summernoteOld = creativeNew.querySelectorAll(".note-editor, .note-frame")[0]
    summernoteOld.remove()

    removeButtonInCreativeNw.classList.remove("invisible")
    removeButtonInCreativeNw.addEventListener("click", () => {
        creativeNew.remove()
    })

    for (let textarea of creativeInCreativeNw) {
        textarea.value = "" // Clear the text inside the textarea
    }

    // divInCreativeNw
    divInCreativeNw.style.display = "block"
    const customID = generateRandomFilename()
    divInCreativeNw.id = customID

    for (let div of previewInCreativeNw) {
        div.textContent = "" // Clear the text inside the div
    }

    creativeContainer.appendChild(creativeNew)

    $(`#${customID}`).summernote({
        placeholder: "Creative...",
        tabsize: 2,
        height: 391,
        minheight: 391,
        spellCheck: false,
        fontNames: ["Arial", "Arial Black", "Comic Sans MS", "Courier New", "Helvetica", "Impact", "Tahoma", "Times New Roman", "Trebuchet MS", "Verdana"],
        toolbar: [
            ["style", ["style"]],
            ["font", ["bold", "underline", "clear"]],
            ["fontname", ["fontname"]],
            ["fontsize", ["fontsize"]],
            ["color", ["color"]],
            ["para", ["ul", "ol", "paragraph"]],
            ["table", ["table"]],
            ["insert", ["link", "picture", "video"]],
            ["view", ["fullscreen", "codeview", "help"]],
        ],
        addDefaultFonts: true,
    }) // Initialize summernote

    const summernotesNw = document.querySelectorAll(".note-editor, .note-frame")

    summernotesNw.forEach((summernoteNw) => {
        summernoteNw.style.padding = "0" // Remove padding
    })
}

function generateRandomFilename() {
    const characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
    const length = 10
    let randomFilename = ""
    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * characters.length)
        randomFilename += characters.charAt(randomIndex)
    }
    return randomFilename
}

function showBlacklistDialogue() {
    let blacklistDialogue = document.getElementById("blacklistDialogue")
    blacklistDialogue.classList.remove("invisible")
}

function closeBlacklistDialogue() {
    let blacklistDialogue = document.getElementById("blacklistDialogue")
    blacklistDialogue.classList.add("invisible")
}

function testNow() {
    test = true
}

function exportTokens(event) {
    const target = event
    const container = target.parentNode.parentNode.querySelector("div")

    // Put innerHTML in the clipboard
    navigator.clipboard.writeText(container.innerHTML)
}

async function importTokens(event) {
    console.log(event)
    const target = event.target
    const pastContainer = target.parentNode.parentNode.querySelector("textarea")
    const buttonsContainer = target.parentNode.parentNode.querySelectorAll("div")[1]
    const okButtonContainer = target.parentNode.parentNode.querySelectorAll("div")[2]

    pastContainer.classList.remove("invisible")
    buttonsContainer.classList.add("d-none")
    okButtonContainer.classList.remove("invisible")
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

        var emailTest = document.getElementById("emailTest").value.split("; ")

        emailTest = emailTest.filter((element) => element !== "")
        start_index = 0

        let recipients = document.getElementById("recipients")
        let recipientsArray = recipients.value.toString().split("\n")
        recipientsArray = recipientsArray.filter((element) => element !== "")
        let recipientsCount = recipientsArray.length

        if (parseInt(recipientsCount) > 50) {
            savehistory()
        }

        lastRecipientCount = 0

        // Send email
        sendEmails()
    })
})

async function sendEmails() {
    var fields = {
        offerID             : document.getElementById("offerID"), // prettier-ignore
        offerName           : document.getElementById("offerName"), // prettier-ignore
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
        fromNames           : document.querySelectorAll(".fromName"), // prettier-ignore
        subjectEncoding     : document.getElementById("subjectEncoding"), // prettier-ignore
        subjects            : document.querySelectorAll(".subject"), // prettier-ignore
        fromEmailCheck      : document.getElementById("fromEmailCheck"), // prettier-ignore
        fromEmail           : document.getElementById("fromEmail"), // prettier-ignore
        replyToCheck        : document.getElementById("replyToCheck"), // prettier-ignore
        replyTo             : document.getElementById("replyTo"), // prettier-ignore
        returnPathCheck     : document.getElementById("returnPathCheck"), // prettier-ignore
        returnPath          : document.getElementById("returnPath"), // prettier-ignore
        link                : document.getElementById("link"), // prettier-ignore
        attachements        : document.getElementById("attachements"), // prettier-ignore
        testAfter           : document.getElementById("testAfter"), // prettier-ignore
        emailTest           : document.getElementById("emailTest"), // prettier-ignore
        start               : document.getElementById("start"), // prettier-ignore
        count               : document.getElementById("count"), // prettier-ignore
        // creatives           : document.querySelectorAll(".creative"), // prettier-ignore
        recipients          : document.getElementById("recipients"), // prettier-ignore
        blacklist           : document.getElementById("blacklist"), // prettier-ignore
        failed              : document.getElementById("failed"), // prettier-ignore
        countryID           : document.getElementById("country"), // prettier-ignore
    }

    var servers         = fields.servers.value.split("\n") // prettier-ignore
    var emailTest       = fields.emailTest.value.split("; ") // prettier-ignore
    var recipients      = fields.recipients.value.split("\n") // prettier-ignore
    var blacklistNodes  = fields.blacklist.childNodes // prettier-ignore

    let blacklist = []
    blacklistNodes.forEach((node) => {
        blacklist.push(node.textContent)
    })

    // From Names
    var fromNames    = fields.fromNames // prettier-ignore
    let fromNameArray = []

    for (let i = 0; i < fromNames.length; i++) {
        fromNameArray.push(fromNames[i].textContent)
    }

    // Subjects
    var subjects    = fields.subjects // prettier-ignore
    let subjectArray = []

    for (let i = 0; i < subjects.length; i++) {
        subjectArray.push(subjects[i].textContent)
    }

    // Creative
    let creativeArray = getCreatives()

    const offerID           = fields.offerID.value // prettier-ignore
    const offerName         = fields.offerName.value // prettier-ignore
    const pauseAfterSend    = fields.pauseAfterSend.value * 1000 // prettier-ignore
    const rotationAfter     = fields.rotationAfter.value * 1000 // prettier-ignore
    let BCCnumber           = fields.BCCnumber.value // prettier-ignore
    const headers           = fields.headers.value // prettier-ignore
    const contentType       = fields.contentType.value // prettier-ignore
    const charset           = fields.charset.value // prettier-ignore
    const encoding          = fields.encoding.value // prettier-ignore
    const priority          = fields.priority.value // prettier-ignore
    const fromNameEncoding  = fields.fromNameEncoding.value // prettier-ignore
    const subjectEncoding   = fields.subjectEncoding.value // prettier-ignore
    const fromEmailCheck    = fields.fromEmailCheck.checked // prettier-ignore
    const fromEmail         = fields.fromEmail.value // prettier-ignore
    const replyToCheck      = fields.replyToCheck.checked // prettier-ignore
    const replyTo           = fields.replyTo.value // prettier-ignore
    const returnPathCheck   = fields.returnPathCheck.checked // prettier-ignore
    const returnPath        = fields.returnPath.value // prettier-ignore
    const link              = fields.link.value // prettier-ignore
    const attachements      = fields.attachements.files // prettier-ignore
    const testAfter         = fields.testAfter.value // prettier-ignore
    let start               = parseInt(fields.start.value) // prettier-ignore
    let count               = fields.count.value // prettier-ignore
    const mailerID          = localStorage.getItem("mailerID") // prettier-ignore
    const countryID         = fields.countryID.value // prettier-ignore

    // Remove empty lines
    servers         = servers.filter((element) => element !== "") // prettier-ignore
    emailTest       = emailTest.filter((element) => element !== "") // prettier-ignore
    recipients      = recipients.filter((element) => element !== "") // prettier-ignore
    blacklist       = blacklist.filter((element) => element !== "") // prettier-ignore
    creativeArray   = creativeArray.filter((element) => element !== "") // prettier-ignore

    // Remove recipients who are in the blacklist
    const filteredRecipients = recipients.filter((recipient) => !blacklist.includes(recipient))

    let modifiedRecipients = []

    // Add test emails to the recipients list
    if (!recipients.length == 0) {
        for (let i = start; i < filteredRecipients.length; i++) {
            modifiedRecipients.push(filteredRecipients[i])

            if ((i + 1) % parseInt(testAfter) === 0) {
                // Add emailTest at the specific index based on testAfter value
                for (let j = 0; j < emailTest.length; j++) {
                    modifiedRecipients.push(emailTest[j])
                }
            }
        }
    } else {
        for (let k = 0; k < emailTest.length; k++) {
            modifiedRecipients.push(emailTest[k])
        }
    }

    let paused = sessionStorage.getItem("paused")

    let end = parseInt(start) - 1 + parseInt(count)
    end = end > modifiedRecipients.length - 1 ? modifiedRecipients.length - 1 : end
    const endElement = filteredRecipients[end]
    count = modifiedRecipients.indexOf(endElement) + 1

    count = filteredRecipients.length == 0 ? modifiedRecipients.length : count

    // Upload history to db
    let status = document.getElementById("status").textContent
    if (status.includes("Pending") || status.includes("Stopped")) {
    }

    // Calculate rotations number
    const sendPerRotation = BCCnumber * servers.length
    const result = modifiedRecipients.length / sendPerRotation
    const nbrRotations = result !== Math.floor(result) ? Math.ceil(result) : result

    // Perform the rotation
    rotation: for (let i = 0; i < nbrRotations; i++) {
        var serverCount = 0
        if (servers.length >= modifiedRecipients.length) {
            if (BCCnumber == modifiedRecipients.length) {
                serverCount = 1
            } else {
                serverCount = modifiedRecipients.length - BCCnumber + 1
            }
        } else {
            serverCount = servers.length
        }

        // Loop through the servers
        for (let j = 0; j < serverCount; j++) {
            let server = servers[j]

            // prettier-ignore
            // Loop through the modifiedRecipients and send emails
            for (let k = start_index; k < start_index + parseInt(BCCnumber); k++) {

                // Stop the loops after finishing all recipients
                if (k + 1 > parseInt(count)) {
                        setTimeout(() => {
                            sendStatus = "completed"
                            controlButtons()
                            sessionStorage.setItem("paused", false)
                        }, 1000)
                        
                        break rotation
                }

                switch (sendStatus) {
                    case "paused":
                        break rotation
                        break;

                    case "stopped":
                        break rotation
                        break;
                
                    default:
                        break;
                }
                
                // Test button
                if (test) {
                    modifiedRecipients.splice(k, 0, ...emailTest)
                    test = false
                    count += emailTest.length
                }

                // set var of the last recipient count and update it only if its greater than the last one
                if ((k + 1) > lastRecipientCount) {
                    lastRecipientCount = k + 1
                }

                let fromName    = fromNameArray[Math.floor(Math.random() * fromNameArray.length)] // Random From Name
                let subject     = subjectArray[Math.floor(Math.random() * subjectArray.length)]   // Random Subject
                let creative    = creativeArray[Math.floor(Math.random() * creativeArray.length)] // Random Creative

                let recipient = modifiedRecipients[k]

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
                    const newFileName = generateRandomFilename() + attachements[l].name.substr(attachements[l].name.lastIndexOf("."))

                    formData.append("attachements[]", attachements[l], newFileName)
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
                        success: function (response) {
                            // Convert the json object to a js object
                            // Convert it again to an array which gives 2 nested arrays
                            // Get the first nested array which contains the email and its response
                            var emailResponse = Object.entries(JSON.parse(response))[0]

                            // Get the second value of the second nested array which contains the status (color of text in response using the bootstrap class)
                            var status = Object.entries(JSON.parse(response))[1][1]

                            // prettier-ignore
                            // Display the response message next to each recipient's email address
                            $("#responseArea").append(
                                `<tr id="responseN${k + 1}">
                                    <th scope="row" style="width: 10%;">
                                        ${k + 1} / ${count}
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
                            var percentage = (lastRecipientCount / count) * 100

                            progressBar.innerText = `${percentage.toFixed(0)}%`
                            progressBar.style.width = `${percentage.toFixed(0)}%`

                            sendCount.innerText = `${lastRecipientCount} / ${count}`

                            // Scroll to the last added response
                            let responseTable = document.getElementById("responseTable")
                            responseTable.scroll(0, responseTable.scrollHeight)
                        },
                    })
                } catch (error) {
                    console.log(error)
                }

                // Set the sendStatus value to completed when reaching last recipient and set buttons properties
                if (k + 1 == count) {
                    setTimeout(() => {
                        sendStatus = "completed"
                        controlButtons()
                        sessionStorage.setItem("paused", false)
                    }, 1000)
                }

                await delay(400)
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

async function uploadHistory(history) {
    // while (data) {
    fetch("http://45.145.6.18/database/uploadHistory.php", {
        method: "POST", // or 'PUT'
        body: history,
    })
        .then((response) => response.json())
        .then((data) => console.log(data))
        .catch((error) => {
            console.log(`Error: ${error}`)
            uploadHistory(history)
        })

    // const data = await result.json()
    // console.log(data)
    // }
}

function savehistory() {
    var fields = {
        offerID             : document.getElementById("offerID"), // prettier-ignore
        offerName           : document.getElementById("offerName"), // prettier-ignore
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
        fromNames           : document.querySelectorAll(".fromName"), // prettier-ignore
        subjectEncoding     : document.getElementById("subjectEncoding"), // prettier-ignore
        subjects            : document.querySelectorAll(".subject"), // prettier-ignore
        fromEmailCheck      : document.getElementById("fromEmailCheck"), // prettier-ignore
        fromEmail           : document.getElementById("fromEmail"), // prettier-ignore
        replyToCheck        : document.getElementById("replyToCheck"), // prettier-ignore
        replyTo             : document.getElementById("replyTo"), // prettier-ignore
        returnPathCheck     : document.getElementById("returnPathCheck"), // prettier-ignore
        returnPath          : document.getElementById("returnPath"), // prettier-ignore
        link                : document.getElementById("link"), // prettier-ignore
        attachements        : document.getElementById("attachements"), // prettier-ignore
        creatives           : document.querySelectorAll(".creative"), // prettier-ignore
        recipients          : document.getElementById("recipients"), // prettier-ignore
        blacklist           : document.getElementById("blacklist"), // prettier-ignore
        failed              : document.getElementById("failed"), // prettier-ignore
        countryID           : document.getElementById("country"), // prettier-ignore
    }

    var servers         = fields.servers.value.split("\n") // prettier-ignore
    var headers         = fields.headers.value.split("\n").join("||") // prettier-ignore
    var recipients      = fields.recipients.value.split("\n") // prettier-ignore
    var blacklistNodes  = fields.blacklist.childNodes // prettier-ignore

    let blacklist = []
    blacklistNodes.forEach((node) => {
        blacklist.push(node.textContent)
    })

    // From Names
    var fromNamesFields = fields.fromNames // prettier-ignore
    let fromNameArray   = [] // prettier-ignore

    for (let i = 0; i < fromNamesFields.length; i++) {
        fromNameArray.push(fromNamesFields[i].textContent)
    }

    // Subjects
    var subjectsField   = fields.subjects // prettier-ignore
    let subjectArray    = [] // prettier-ignore

    for (let i = 0; i < subjectsField.length; i++) {
        subjectArray.push(subjectsField[i].textContent)
    }

    // Creative
    let creativeArray = getCreatives()

    const offerID           = fields.offerID.value // prettier-ignore
    const offerName         = fields.offerName.value // prettier-ignore
    const contentType       = fields.contentType.value // prettier-ignore
    const charset           = fields.charset.value // prettier-ignore
    const encoding          = fields.encoding.value // prettier-ignore
    const priority          = fields.priority.value // prettier-ignore
    const fromNameEncoding  = fields.fromNameEncoding.value // prettier-ignore
    const subjectEncoding   = fields.subjectEncoding.value // prettier-ignore
    const fromEmailCheck    = fields.fromEmailCheck.checked // prettier-ignore
    const fromEmail         = fields.fromEmail.value // prettier-ignore
    const replyToCheck      = fields.replyToCheck.checked // prettier-ignore
    const replyTo           = fields.replyTo.value // prettier-ignore
    const returnPathCheck   = fields.returnPathCheck.checked // prettier-ignore
    const returnPath        = fields.returnPath.value // prettier-ignore
    const link              = fields.link.value // prettier-ignore
    const attachements      = fields.attachements.files // prettier-ignore
    const mailerID          = localStorage.getItem("mailerID") // prettier-ignore
    const countryID         = fields.countryID.value // prettier-ignore

    // Remove empty lines
    servers         = servers.filter((element) => element !== "") // prettier-ignore
    recipients      = recipients.filter((element) => element !== "") // prettier-ignore
    blacklist       = blacklist.filter((element) => element !== "") // prettier-ignore
    creativeArray   = creativeArray.filter((element) => element !== "") // prettier-ignore

    let fromNames = fromNameArray.join("||") // From Names
    let subjects = subjectArray.join("||") // Subjects
    let creatives = creativeArray.join("||||") // Creatives

    // Define a history FormData and populate with necessary infos and pass it to the uploadHistory() function to upload
    let history = new FormData()
    history.append("offerID", offerID)
    history.append("offerName", offerName)
    history.append("servers", servers)
    history.append("header", headers)
    history.append("contentType", contentType)
    history.append("charset", charset)
    history.append("encoding", encoding)
    history.append("priority", priority)
    history.append("fromName", fromNames)
    history.append("fromNameEncoding", fromNameEncoding)
    history.append("subject", subjects)
    history.append("subjectEncoding", subjectEncoding)
    history.append("fromEmailCheck", fromEmailCheck)
    history.append("fromEmail", fromEmail)
    history.append("replyToCheck", replyToCheck)
    history.append("replyTo", replyTo)
    history.append("returnPathCheck", returnPathCheck)
    history.append("returnPath", returnPath)
    history.append("link", link)
    history.append("creative", `${creatives}`)
    history.append("recipients", recipients)
    history.append("blacklist", blacklist)
    history.append("mailerID", mailerID)
    history.append("countryID", countryID)

    for (let l = 0; l < attachements.length; l++) {
        history.append("attachements[]", attachements[l])
    }

    // Upload history to db
    uploadHistory(history)
}
