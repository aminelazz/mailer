window.addEventListener("message", receiveMessage, false)

const serverContainer = document.getElementById("serverContainer")

const serverTokenExample = document.getElementById("serverTokenExample")
const serverToken = serverTokenExample.cloneNode(true)
serverTokenExample.remove()
serverToken.id = ""
serverToken.classList.remove("d-none")

// Create a new MutationObserver to watch for changes of tokens in the serverContainer
const observer = new MutationObserver(function (mutationsList, observer) {
    // Handle the change event here
    const serverNumbers = serverContainer.querySelectorAll(".server-token > .server-nbr")
    const serverLabels = serverContainer.querySelectorAll(".server-token > .label")

    const serverNumbersArray = Array.from(serverNumbers)
    const serverLabelsArray = Array.from(serverLabels)

    // console.log(serverNumbersArray)

    serverNumbersArray.forEach((serverNumber, index) => {
        serverNumber.textContent = index + 1
    })

    serverLabelsArray.forEach((serverLabel, index) => {
        serverLabel.setAttribute("data-index", index)
    })
    // console.log("Content of the <div> changed")

    // You can add your custom logic here to respond to the change
    // For example, trigger a custom event or call a function
})

// Configure the MutationObserver to watch for changes of tokens in the serverContainer
const config = { childList: true, subtree: false }

// Start observing the target element with the configured options
observer.observe(serverContainer, config)

// Initialize tooltips
// const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
// const tooltipList = [...tooltipTriggerList].map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))

const historyIframe = document.getElementById("historyIframe")
const dataIframe = document.getElementById("dataIframe")
const imapIframe = document.getElementById("imapIframe")
const trackingIframe = document.getElementById("trackingIframe")
const smtpCheckIframe = document.getElementById("smtpCheckIframe")

const navSendTab = document.getElementById("nav-send-tab")
const navHistoryTab = document.getElementById("nav-history-tab")
const navDataTab = document.getElementById("nav-data-tab")
const navImapTab = document.getElementById("nav-imap-tab")
const navTrackingTab = document.getElementById("nav-tracking-tab")
const navSmtpCheckTab = document.getElementById("nav-smtp-check-tab")

const dropZone = document.getElementById("dropZone")
const fileInput = document.getElementById("data")

var nbrRotations
var scheduled = false

async function receiveMessage(event) {
    // console.log(event.data)
    // Load data from the child page
    if (event.data.offerData) {
        let offerData = event.data.offerData
        console.log(offerData)
        await organizeData(offerData)
    }

    // Get valid SMTPs from the child page
    if (event.data.validSMTPs) {
        serverContainer.innerHTML = ""

        let validSMTPs = event.data.validSMTPs
        validSMTPs.forEach((server) => {
            addServerToken({ key: "Enter", servers: server, preventDefault: () => {} })
        })
        document.getElementById("nav-send-tab").click()
    }
}

async function organizeData(data) {
    // Update the fields in the parent page based on the received data
    document.getElementById("servers").innerHTML = data.servers.replaceAll(",", "\n") // prettier-ignore
    document.getElementById("pauseAfterSend").value = data.pauseAfterSend // prettier-ignore
    document.getElementById("rotationAfter").value = data.rotationAfter // prettier-ignore
    document.getElementById("BCCnumber").value = data.BCCnumber // prettier-ignore
    document.getElementById("headers").innerHTML = data.header.replaceAll("||", "\n") // prettier-ignore
    document.getElementById("contentType").value = data.contentType // prettier-ignore
    document.getElementById("charset").value = data.charset // prettier-ignore
    document.getElementById("encoding").value = data.encoding // prettier-ignore
    document.getElementById("priority").value = data.priority // prettier-ignore
    document.getElementById("offerID").value = data.offerID // prettier-ignore
    document.getElementById("offerName").value = data.offerName // prettier-ignore
    document.getElementById("country").value = data.countryID // prettier-ignore
    document.getElementById("fromNameEncoding").value = data.fromNameEncoding // prettier-ignore
    document.getElementById("subjectEncoding").value = data.subjectEncoding // prettier-ignore
    document.getElementById("fromEmailCheck").checked = !!+data.fromEmailCheck // prettier-ignore
    document.getElementById("fromEmail").value = data.fromEmail // prettier-ignore
    document.getElementById("replyToCheck").checked = !!+data.replyToCheck // prettier-ignore
    document.getElementById("replyTo").value = data.replyTo // prettier-ignore
    document.getElementById("returnPathCheck").checked = !!+data.returnPathCheck // prettier-ignore
    document.getElementById("returnPath").value = data.returnPath // prettier-ignore
    document.getElementById("link").value = data.link // prettier-ignore
    // document.getElementById("recipients").innerHTML = data.recipients.replaceAll(",", "\n") // prettier-ignore
    // document.getElementById("blacklist").innerHTML = data.blacklist.replaceAll(",", "\n") // prettier-ignore

    let fromNamesField = document.getElementsByClassName("fromNames")[0] // prettier-ignore
    let subjectsField = document.getElementsByClassName("subjects")[0] // prettier-ignore
    let creativeFields = document.getElementsByClassName("creative") // prettier-ignore

    let fromNames = data.fromName.split("||")
    let subjects = data.subject.split("||")
    let creatives = data.creative.split("||||")

    // Servers
    serverContainer.innerHTML = ""

    const servers = data.servers.split(",")
    servers.forEach((server) => {
        addServerToken({ key: "Enter", servers: server, preventDefault: () => {} })
    })

    // From Names
    fromNamesField.innerHTML = ""

    if (fromNames.length == 1 && fromNames[0] == "") {
        fromNames = []
    }
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

    if (subjects.length == 1 && subjects[0] == "") {
        subjects = []
    }
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
    var serversField = document.getElementById("servers")
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
        var writeType = document.getElementById("writeType")

        writeType.selectedIndex = 0 // Select the first option

        statusLabel.innerText = "Status: Pending"

        // Clear the failed area
        failed.innerText = ""

        // hide the clear attachements button
        if (attachementsName.value != "") {
            clearButton.classList.toggle("invisible")
        }

        // initiate summernote editor
        const summernotes = document.querySelectorAll(".note-editor, .note-frame")
        summernotes.forEach((summernote) => {
            summernote.style.padding = "0"
        })

        // organize copy/paste/edit buttons of from names and subjects
        if (navigator.clipboard && navigator.clipboard.readText) {
            let copy_pasteButtons = document.querySelectorAll(".copy_paste")
            copy_pasteButtons.forEach((button) => {
                button.classList.remove("d-none")
            })
            let editButtons = document.querySelectorAll(".edit")
            editButtons.forEach((button) => {
                button.classList.add("d-none")
                button.classList.remove("rounded-end", "rounded-bottom-0")
            })
        } else {
            let copy_pasteButtons = document.querySelectorAll(".copy_paste")
            copy_pasteButtons.forEach((button) => {
                button.classList.add("d-none")
            })
            let editButtons = document.querySelectorAll(".edit")
            editButtons.forEach((button) => {
                button.classList.remove("d-none")
                button.classList.add("rounded-end", "rounded-bottom-0")
            })
        }

        fileUpload()
        clearFiles()
        configureRecipientsBlacklist()

        initializeTooltip()

        // Add default values to the fields
        addToken({ key: "Enter", target: document.getElementById("fromName"), preventDefault: () => {} })
        addToken({ key: "Enter", target: document.getElementById("subject"), preventDefault: () => {} })
    })
} catch (error) {}

// Start index of recipients
var start_index = 0
var counter = 0

var statusLabel = document.getElementById("status")

window.addEventListener("beforeunload", preventUnload)

function preventUnload(event) {
    event.preventDefault()
    event.returnValue = "Are you sure you want to exit?"
}

function initializeTooltip(boundary = null) {
    boundary = boundary || document.body

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(
        (tooltipTriggerEl) =>
            new bootstrap.Tooltip(tooltipTriggerEl, {
                boundary: boundary, // or document.querySelector('#boundary')
            })
    )
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
    let serverInput = document.getElementById("servers")
    let servers = document.querySelectorAll("#serverContainer > .server-token")
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

    // Servers validity
    serverInput.setCustomValidity("")

    if (servers.length == 0) {
        serverInput.setCustomValidity("Please enter at least one valid server")
    }

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

    // Split the text by new line
    pastedText = pastedText.split("\n")

    // Remove empy value from array
    pastedText = pastedText.filter((element) => element !== "")

    let content = ""

    // for each element of array add to it the div html attributes and br
    pastedText.forEach((element) => {
        element = `<div>${element}<br></div>`
        content += element
    })

    blacklistField.innerHTML = content

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
    let historyIframe = document.getElementById("historyIframe")
    historyIframe.src = historyIframe.src
}

function addToken(event) {
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

function addServerToken(event) {
    const target = event.target ? event.target : null
    // console.log(event)
    if (event.key === "Enter") {
        event.preventDefault()

        // console.log(target.value)

        const servers = target ? target.value : event.servers
        if (servers.trim() !== "") {
            const serverArray = servers.split(" ")

            // console.log(serverArray)

            for (let i = 0; i < serverArray.length; i++) {
                const server = serverArray[i]
                // servers validity
                let serverpattern = /^(?:[\w.-]+:\d+:(?:tls|ssl|):[\w.-]+@[\w.-]+:\S+)$/gim

                let invalid = false

                if (!serverpattern.test(server.toString())) {
                    invalid = true
                }

                if (invalid) {
                    alert("Please enter a valid server")
                    break
                }

                const tokenContainer = document.getElementById("serverContainer")

                const token = serverToken.cloneNode(true)
                token.setAttribute("data-value", server)

                const label = token.querySelector(".label")
                label.textContent = server

                tokenContainer.appendChild(token)
            }
            target ? (target.value = "") : "" // Clear the input after creating the token
        }
    }
}

function pasteServers(event) {
    event.preventDefault()

    let pastedText = ""
    try {
        pastedText = (event.clipboardData || window.clipboardData).getData("text")
    } catch (error) {
        pastedText = event.target.value
    }

    const servers = pastedText.split("\n")
    servers.forEach((server) => {
        addServerToken({ key: "Enter", servers: server, preventDefault: () => {} })
    })
}

function getCreatives() {
    let creatives = document.getElementsByClassName("creative")
    let creativeArray = []

    for (let i = 0; i < creatives.length; i++) {
        const creativesContainer = creatives[i].parentNode.parentNode
        const subcreativesContainer = creativesContainer.querySelector(".subcreativesContainer")
        const subcreatives = subcreativesContainer.querySelectorAll(".subcreative")

        let creative = $(".creative").eq(i).summernote("code")
        if (creative.trim() == "") {
            continue
        }
        let subcreativesArray = []
        subcreatives.forEach((subcreative) => {
            if (subcreative.value.trim() !== "") {
                const subcreativeAttachement = subcreative.parentNode.querySelector("input[type=file]")
                let subcreativeObject = {
                    subcreative: subcreative.value,
                    attachement: subcreativeAttachement.files[0],
                }
                subcreativesArray.push(subcreativeObject)
            }
        })

        const creativeObject = {
            creative: creative,
            subcreatives: subcreativesArray,
        }

        creativeArray.push(creativeObject)
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
    const divInCreativeNw = creativeNew.querySelectorAll("div>div>div>div")[0]
    const removeButtonInCreativeNw = creativeNew.querySelectorAll(".removeCreative")[0]
    const summernoteOld = creativeNew.querySelectorAll(".note-editor, .note-frame")[0]
    summernoteOld.remove()

    const subcreativesContainer = creativeNew.querySelectorAll(".subcreativesContainer")[0]
    const fileContainers = creativeNew.querySelectorAll(".fileContainer")

    fileContainers.forEach((fileContainer) => {
        const fileInput = fileContainer.querySelector("input[type=file]")
        const fileLabel = fileContainer.querySelector("label")
        const generatedID = generateRandomFilename()

        fileInput.id = generatedID
        fileLabel.setAttribute("for", generatedID)
    })

    // Separator
    const hr = document.createElement("hr")
    hr.classList.add("my-3")

    removeButtonInCreativeNw.classList.remove("invisible")
    removeButtonInCreativeNw.addEventListener("click", () => {
        creativeNew.remove()
        hr.remove()
    })

    // divInCreativeNw
    divInCreativeNw.style.display = "block"
    const customID = generateRandomFilename()
    divInCreativeNw.id = customID

    creativeContainer.appendChild(hr)
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

function deleteTokensEvent(tokens) {
    tokens.forEach((token) => {
        const deleteButton = token.querySelector("button")
        deleteButton.addEventListener("click", () => {
            token.remove()
        })
    })
}

function copyTokens(event) {
    const target = event
    const container = target.parentNode.parentNode.querySelector("div")
    // Put innerHTML in the clipboard
    navigator.clipboard.writeText(container.innerHTML)

    target.classList.replace("idle", "ok")
    target.querySelector("svg:first-child").classList.add("d-none")
    target.querySelector("svg:last-child").classList.remove("d-none")

    setTimeout(() => {
        target.classList.replace("ok", "idle")

        target.querySelector("svg:first-child").classList.remove("d-none")
        target.querySelector("svg:last-child").classList.add("d-none")
    }, 2000)
}

async function pasteTokens(event) {
    const target = event
    console.log("Clipboard API available")
    const clipboardText = await navigator.clipboard.readText()
    // console.log(clipboardText)
    const container = target.parentElement.parentElement.querySelector("div")

    // Check if the clipboard content is a valid tokens
    try {
        let div = document.createElement("div")
        div.innerHTML = clipboardText
        const tokens = div.querySelectorAll(".token")
        if (tokens.length == 0) {
            throw new Error("Invalid tokens")
        } else {
            container.innerHTML += clipboardText

            target.classList.replace("idle", "ok")
            target.querySelector("svg:first-child").classList.add("d-none")
            target.querySelector("svg:last-child").classList.remove("d-none")

            setTimeout(() => {
                target.classList.replace("ok", "idle")

                target.querySelector("svg:first-child").classList.remove("d-none")
                target.querySelector("svg:last-child").classList.add("d-none")
            }, 2000)
        }
    } catch (error) {
        console.error(`Error: ${error.message}`)
    }

    // Add event listener to the x button of each token to remove it
    const tokens = target.parentNode.parentNode.querySelectorAll(".token")
    deleteTokensEvent(tokens)
}

function editTokens(event) {
    const target = event
    const allContainer = target.parentNode.parentNode.parentNode
    const tokensDivContainer = allContainer.children[0]
    const tokensDiv = tokensDivContainer.children[0]

    const pastContainer = allContainer.children[1]
    const pastetextarea = pastContainer.children[0]

    tokensDivContainer.classList.replace("d-flex", "d-none")
    pastContainer.classList.replace("d-none", "d-flex")

    pastetextarea.value = tokensDiv.innerHTML
    pastetextarea.focus()
    pastetextarea.select()
}

function clearTokens(event) {
    const target = event
    const container = target.parentNode.parentNode.querySelector("div")

    container.innerHTML = ""
}

function okTokens(event) {
    const target = event
    const allContainer = target.parentNode.parentNode.parentNode
    const tokensDivContainer = allContainer.children[0]
    const tokensDiv = tokensDivContainer.children[0]

    const pastContainer = allContainer.children[1]
    const pastetextarea = pastContainer.children[0]

    // Check if the clipboard content is a valid tokens
    try {
        let div = document.createElement("div")
        div.innerHTML = pastetextarea.value
        const tokens = div.querySelectorAll(".token")
        if (tokens.length == 0) {
            throw new Error("Invalid tokens")
        } else {
            // Check if div has TEXTNODE
            div.childNodes.forEach((child) => {
                if (child.nodeType == 3) {
                    throw new Error("Invalid tokens")
                }
            })
            tokensDiv.innerHTML = pastetextarea.value
        }
    } catch (error) {
        console.error(`Error: ${error.message}`)
    }

    // Add event listener to the x button of each token to remove it
    const tokens = tokensDiv.querySelectorAll(".token")
    deleteTokensEvent(tokens)

    tokensDivContainer.classList.replace("d-none", "d-flex")
    pastContainer.classList.replace("d-flex", "d-none")
}

function changeRcptsWriteType(event) {
    const target = event
    const rcptsWriteType = target.value
    const rcptsTextarea = document.getElementById("recipients")
    const rcptsDragnDrop = document.getElementById("dropZone")
    const rcptsDBContainer = document.getElementById("dbContainer")

    const dbRefreshBtn = document.getElementById("dbRefreshBtn")

    switch (rcptsWriteType) {
        case "manual":
            rcptsTextarea.classList.remove("d-none")
            rcptsDragnDrop.classList.replace("d-flex", "d-none")
            rcptsDBContainer.classList.add("d-none")
            dbRefreshBtn.classList.add("invisible")
            break

        case "drag":
            rcptsTextarea.classList.add("d-none")
            rcptsDragnDrop.classList.replace("d-none", "d-flex")
            rcptsDBContainer.classList.add("d-none")
            dbRefreshBtn.classList.add("invisible")
            break

        case "db":
            rcptsTextarea.classList.add("d-none")
            rcptsDragnDrop.classList.replace("d-flex", "d-none")
            rcptsDBContainer.classList.remove("d-none")
            dbRefreshBtn.classList.remove("invisible")
            break

        default:
            break
    }
}

// Drop Zone of Recipients field
function handleDragOver(event) {
    event.preventDefault()
    dropZone.classList.add("drag-over")
    event.dataTransfer.dropEffect = "copy"
}

function handleDragLeave(event) {
    event.preventDefault()
    dropZone.classList.remove("drag-over")
}

function handleDrop(event) {
    event.preventDefault()
    dropZone.classList.remove("drag-over")
    const file = event.dataTransfer.files[0]
    const fileExtension = file.name.split(".").pop()

    // Check file type is .txt or .csv
    if (fileExtension === "txt" || fileExtension === "csv") {
        fileInput.files = event.dataTransfer.files

        dropZone.classList.add("d-none")

        // Show the recipients field
        recipientsField.classList.remove("d-none")

        writeRecipients(file)
        // Read file content
        // const reader = new FileReader()

        // reader.onload = function (e) {
        //   const fileContent = e.target.result
        //   console.log(fileContent) // or do something else with the content
        // }

        // reader.readAsText(fileInput.files[0])
    } else {
        alert("Please upload a .txt or .csv file")
    }
}

function handleFileInput(event) {
    const file = event.target.files[0]

    dropZone.classList.add("d-none")

    // Show the recipients field
    recipientsField.classList.remove("d-none")

    writeRecipients(file)
}

function writeRecipients(file) {
    let recipients = document.getElementById("recipients")

    // Read file content
    const reader = new FileReader()
    reader.onload = function (e) {
        const fileContent = e.target.result
        // console.log(fileContent) // or do something else with the content
        recipients.value = fileContent
        configureRecipientsBlacklist()
    }
    reader.readAsText(file)
}

async function readFileAsync(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader()

        reader.onload = function (e) {
            const fileContent = e.target.result
            resolve(fileContent)
        }

        reader.onerror = function (e) {
            reject(e.target.error)
        }

        reader.readAsText(file)
    })
}

// DB Field functions
async function refreshFromDB(event) {
    const countryID = document.getElementById("country").value

    const refreshBtn = event.target
    const dbIcon = document.getElementById("dbIcon")
    const dbLoader = document.getElementById("dbLoader")

    if (countryID == "") {
        alert("Please select a country first")
        return
    }

    refreshBtn.disabled = true
    dbIcon.classList.add("d-none")
    dbLoader.classList.remove("d-none")

    const result = await fetch(`https://45.145.6.18/database/data/php/data.php?countryID=${countryID}`)
    const response = await result.json()

    console.log(response)

    refreshBtn.disabled = false
    dbIcon.classList.remove("d-none")
    dbLoader.classList.add("d-none")

    if (response.status != "success") {
        alert(`${response.status}\n\n${response.message}`)
        return
    }
    displayData(response.data)
}

function displayData(data) {
    const db = document.getElementById("db")
    db.querySelector("#selectPlaceholder").classList.add("d-none")

    data.forEach((element) => {
        const id = element.id
        const name = element.name
        const nbrRecipients = `${element.nbrRecipients} recipients`

        // Create a new data entry and set its attributes
        const dataEntry = document.getElementById("dataEntryExample").cloneNode(true)
        dataEntry.id = ""
        dataEntry.setAttribute("data-id", id)
        dataEntry.classList.replace("d-none", "d-flex")

        // Add event listener to the data entry
        dataEntry.addEventListener("click", (event) => {
            event.stopPropagation()
            event.preventDefault()

            const dataEntries = db.querySelectorAll(".dataEntry")
            dataEntries.forEach((dataEntry) => {
                dataEntry.setAttribute("data-selected", false)
            })

            dataEntry.setAttribute("data-selected", true)

            // Set the recipients number
            const nbrRecipients = document.getElementById("nbrRecipients")
            nbrRecipients.textContent = element.nbrRecipients
        })

        // Set the data entry content
        const dataName = dataEntry.querySelector(".dataName")
        const dataNbrRecipients = dataEntry.querySelector(".dataNbrRecipients")
        const loadRecipientBtn = dataEntry.querySelector(".loadRecipientBtn")

        dataName.textContent = name
        dataNbrRecipients.textContent = nbrRecipients
        loadRecipientBtn.addEventListener("click", (event) => loadDataToRecipientsField(event, id, element.nbrRecipients))

        const tooltip = new bootstrap.Tooltip(loadRecipientBtn, {
            boundary: loadRecipientBtn, // or document.querySelector('#boundary')
        })

        // Append the data entry to the db div
        db.appendChild(dataEntry)
    })
}

async function loadDataToRecipientsField(event, id, nbrRecipients) {
    event.stopPropagation()

    const recipientsLoader = document.getElementById("recipientsLoader")
    recipientsLoader.classList.remove("invisible")

    // Load data to the recipients field
    const result = await fetch(`https://45.145.6.18/database/data/php/download_data.php?id=${id}`)
    const response = await result.json()

    recipientsField.value = response.data

    configureRecipientsBlacklist()

    let writeType = document.getElementById("writeType")
    writeType.selectedIndex = 0
    changeRcptsWriteType(writeType)

    recipientsLoader.classList.add("invisible")
    // console.log(response)
}

function setSchedule(setButton) {
    const schedule = document.getElementById("schedule").value
    const startSend = document.getElementById("startSend")
    const cancelSchedule = document.getElementById("cancelSchedule")

    // convert schedule to date object
    const scheduleDate = new Date(schedule)
    // test if the date is bigger than now
    const now = new Date()
    if (scheduleDate <= now) {
        alert("Please select a date bigger than now")
        return
    }

    setButton.disabled = true
    cancelSchedule.classList.remove("d-none")

    scheduled = true

    cancelSchedule.addEventListener("click", () => {
        scheduled = false
        updateCountdown()
    })

    function updateCountdown() {
        const currentTime = new Date()
        const timeDifference = scheduleDate - currentTime

        if (timeDifference <= 0 || scheduled == false) {
            // Timer has reached zero or went negative
            clearInterval(timer)
            setButton.textContent = "Set"
            setButton.disabled = false
            cancelSchedule.classList.add("d-none")
            if (timeDifference <= 0) startSend.click()
        } else {
            // Calculate remaining time
            // 1 hour = 3600000 milliseconds
            const hours = Math.floor(timeDifference / 3600000).toString().padStart(2, '0') //prettier-ignore
            // 1 minute = 60000 milliseconds
            const minutes = Math.floor((timeDifference % 3600000) / 60000).toString().padStart(2, '0') //prettier-ignore
            // 1 second = 1000 milliseconds
            const seconds = Math.floor((timeDifference % 60000) / 1000).toString().padStart(2, '0') //prettier-ignore

            // Display the remaining time
            setButton.textContent = `${hours}:${minutes}:${seconds}`
        }
    }

    // Update the countdown every second
    const timer = setInterval(updateCountdown, 1000)

    // Initial update
    updateCountdown()
}

function showSubcreativeAttachementName(event) {
    const target = event.target
    const subcreativeAttachementLabel = target.parentNode.querySelector(".subcreativeAttachementLabel")
    subcreativeAttachementLabel.textContent = target.files[0].name
    subcreativeAttachementLabel.classList.replace("btn-secondary", "btn-success")
}

function clearSubcreativeAttachement(event) {
    const target = event
    const subcreativeAttachementLabel = target.parentNode.querySelector(".subcreativeAttachementLabel")
    subcreativeAttachementLabel.textContent = "Choose file"
    subcreativeAttachementLabel.classList.replace("btn-success", "btn-secondary")

    const subcreativeAttachement = target.parentNode.querySelector("input[type=file]")
    subcreativeAttachement.value = ""
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
        failed.innerText = ""

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

        // Calculate remaining time
        displayRemainingTime()

        // Cancel schedule if exists
        const cancelSchedule = document.getElementById("cancelSchedule")
        cancelSchedule.click()
    })
})

async function sendEmails() {
    var fields = {
        offerID             : document.getElementById("offerID"), // prettier-ignore
        offerName           : document.getElementById("offerName"), // prettier-ignore
        servers             : document.querySelectorAll(".server-token > .label"), // prettier-ignore
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
        tracking            : document.getElementById("tracking"), // prettier-ignore
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

    var emailTest       = fields.emailTest.value.split("; ") // prettier-ignore
    var recipients      = fields.recipients.value.split("\n") // prettier-ignore
    var blacklistNodes  = fields.blacklist.childNodes // prettier-ignore

    let blacklist = []
    blacklistNodes.forEach((node) => {
        blacklist.push(node.textContent)
    })

    //Servers
    const serversLabel = fields.servers // prettier-ignore
    let servers = [] // prettier-ignore

    for (let i = 0; i < serversLabel.length; i++) {
        servers.push(serversLabel[i].textContent)
    }

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
    const tracking          = fields.tracking.checked // prettier-ignore
    const link              = fields.link.value // prettier-ignore
    const attachements      = fields.attachements.files // prettier-ignore
    const testAfter         = fields.testAfter.value // prettier-ignore
    let start               = parseInt(fields.start.value) // prettier-ignore
    let count               = fields.count.value // prettier-ignore
    const mailerID          = localStorage.getItem("mailerID") // prettier-ignore
    const countryID         = fields.countryID.value // prettier-ignore

    // Get current date and time and format it like (YYYY-MM-DD HH:MM:SS)
    const date = new Date()
    const year = date.getFullYear()
    const month = date.getMonth() + 1
    const day = date.getDate()
    const hours = date.getHours()
    const minutes = date.getMinutes()
    const seconds = date.getSeconds()
    const currentDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`

    // Remove empty lines
    servers         = servers.filter((element) => element !== "") // prettier-ignore
    emailTest       = emailTest.filter((element) => element !== "") // prettier-ignore
    recipients      = recipients.filter((element) => element !== "") // prettier-ignore
    blacklist       = blacklist.filter((element) => element !== "") // prettier-ignore

    // Creative
    let creativeArray = getCreatives()
    // console.log(creativeArray)
    let UID = generateUID()
    // console.log(`Generated UID: ${UID}`)
    if (tracking) {
        const subData = {
            offerName           : offerName, // prettier-ignore
            headers             : headers, // prettier-ignore
            servers             : servers, // prettier-ignore
            contentType         : contentType, // prettier-ignore
            charset             : charset, // prettier-ignore
            encoding            : encoding, // prettier-ignore
            priority            : priority, // prettier-ignore
            fromNameEncoding    : fromNameEncoding, // prettier-ignore
            fromNames           : fromNameArray, // prettier-ignore
            subjectEncoding     : subjectEncoding, // prettier-ignore
            subjects            : subjectArray, // prettier-ignore
            fromEmailCheck      : fromEmailCheck, // prettier-ignore
            fromEmail           : fromEmail, // prettier-ignore
            replyToCheck        : replyToCheck, // prettier-ignore
            replyTo             : replyTo, // prettier-ignore
            returnPathCheck     : returnPathCheck, // prettier-ignore
            returnPath          : returnPath, // prettier-ignore
            tracking            : tracking, // prettier-ignore
            link                : link, // prettier-ignore
            countryID           : countryID, // prettier-ignore
            date                : currentDate, // prettier-ignore
        }

        // Save the subData in the database
        saveSubcreatives(creativeArray, UID, subData)
    }

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

    // Calculate rotations number
    const sendPerRotation = BCCnumber * servers.length
    const result = modifiedRecipients.length / sendPerRotation
    nbrRotations = result !== Math.floor(result) ? Math.ceil(result) : result

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

                    case "stopped":
                        break rotation
                
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

                let fromName        = fromNameArray[Math.floor(Math.random() * fromNameArray.length)] // Random From Name
                let subject         = subjectArray[Math.floor(Math.random() * subjectArray.length)]   // Random Subject
                let creativeIndex   = Math.floor(Math.random() * creativeArray.length) // Random Creative

                let creativeObject      = creativeArray[creativeIndex]
                let creative            = creativeObject.creative

                let recipient = modifiedRecipients[k]

                let formData = new FormData()
                formData.append("UID", UID)
                formData.append("offerName", offerName)
                formData.append("serverIndex", j)
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
                formData.append("tracking", tracking)
                formData.append("link", link)
                formData.append("creative", creative)
                formData.append("creativeID", creativeIndex)
                formData.append("recipient", recipient)
                formData.append("countryID", countryID)
                formData.append("date", currentDate)
                
                // Add main attachements
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
                            // console.log(response)

                            const status = response.status
                            const message = response.message
                            const email = response.email
                            const server = response.server
                            const serverIndex = parseInt(response.serverIndex)

                            // prettier-ignore
                            // Display the response message next to each recipient's email address
                            $("#responseArea").append(
                                `<tr id="responseN${k + 1}">
                                    <th scope="row" style="width: 10%;">
                                        ${k + 1} / ${count}
                                    </th>
                                    <td class="text-center" style="width: 30%;">
                                        ${email}
                                    </td>
                                    <td class="text-center fw-semibold" title="${server}" style="width: 10%;">
                                        SMTP N° ${serverIndex + 1}
                                    </td>
                                    <td class="text-${status}">
                                        <div class="text-center fw-semibold response" title="${message}">
                                            ${message}
                                        <div>
                                    </td>
                                    <td class="text-center" style="width: 10%;">
                                        ${formattedHours}:${formattedMinutes}:${formattedSeconds}
                                    </td>
                                </tr>`
                            )

                            // Fill the Bounced area with failed sends
                            if (status == "danger") {
                                fields.failed.innerText += `${email}\n`
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

                // Show notification when email test is sent
                // if (recipient = emailTest[0]) {
                //     $.notify(
                //         {
                //             // options
                //             icon: "fas fa-check-circle",
                //             title: "<strong>Test Email Sent</strong>",
                //             message: `Test email has been sent to ${emailTest}`,
                //         },
                //         {
                //             // settings
                //             type: "success",
                //             placement: {
                //                 from: "top",
                //                 align: "center",
                //             },
                //             animate: {
                //                 enter: "animated fadeInDown",
                //                 exit: "animated fadeOutUp",
                //             },

                //             delay: 5000,
                //             template:
                //                 '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
                //                 '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                //                 '<span data-notify="icon"></span> ' +
                //                 '<span data-notify="title">{1}</span> ' +
                //                 '<span data-notify="message">{2}</span>' +
                //                 "</div>",


                //         }
                //     )
                // }



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
    fetch("https://45.145.6.18/database/uploadHistory.php", {
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
        servers             : document.querySelectorAll(".server-token > .label"), // prettier-ignore
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

    //Servers
    const serversLabel = fields.servers // prettier-ignore
    let serversArray = [] // prettier-ignore

    for (let i = 0; i < serversLabel.length; i++) {
        serversArray.push(serversLabel[i].textContent)
    }

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
    recipients      = recipients.filter((element) => element !== "") // prettier-ignore
    blacklist       = blacklist.filter((element) => element !== "") // prettier-ignore
    creativeArray   = creativeArray.filter((element) => element !== "") // prettier-ignore

    const servers = serversArray.join(",") // prettier-ignore
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
    // history.append("recipients", recipients)
    // history.append("blacklist", blacklist)
    history.append("mailerID", mailerID)
    history.append("countryID", countryID)

    for (let l = 0; l < attachements.length; l++) {
        history.append("attachements[]", attachements[l])
    }

    // Upload history to db
    uploadHistory(history)
}

function displayRemainingTime() {
    const remainingLabel = document.getElementById("remainingLabel")
    const pauseAfterSend = parseInt(document.getElementById("pauseAfterSend").value)
    const rotationAfter = parseInt(document.getElementById("rotationAfter").value)

    //Servers
    const serversLabel = document.querySelectorAll(".server-token > .label") // prettier-ignore
    let serversArray = [] // prettier-ignore

    for (let i = 0; i < serversLabel.length; i++) {
        serversArray.push(serversLabel[i].textContent)
    }

    const serversLength = parseInt(serversArray.length)

    const timePerRotation = serversLength * pauseAfterSend + rotationAfter
    let totalTime = timePerRotation * nbrRotations
    let totalTimeDate

    function updateRemainingTime() {
        // console.log(`remaining time: ${totalTime}`)
        totalTime = parseInt(totalTime) - 1
        if (totalTime < 0) {
            clearInterval(timer)
            return
        }
        totalTimeDate = new Date(totalTime * 1000).toISOString().substr(11, 8)
        remainingLabel.textContent = `Remaining Time: ${totalTimeDate}`
    }

    // Update the remaining time every second
    const timer = setInterval(updateRemainingTime, 1000)
}

function editDocumentData() {
    var fields = {
        offerID             : document.getElementById("offerID"), // prettier-ignore
        offerName           : document.getElementById("offerName"), // prettier-ignore
        servers             : document.querySelectorAll(".server-token > .label"), // prettier-ignore
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
        creatives           : document.querySelectorAll(".creative"), // prettier-ignore
        countryID           : document.getElementById("country"), // prettier-ignore
    }

    //Servers
    const serversLabel = fields.servers // prettier-ignore
    let serversArray = [] // prettier-ignore

    for (let i = 0; i < serversLabel.length; i++) {
        serversArray.push(serversLabel[i].textContent)
    }

    // From Names
    var fromNamesFields = fields.fromNames // prettier-ignore
    let fromNameArray   = [] // prettier-ignore

    for (let i = 0; i < fromNamesFields.length; i++) {
        fromNameArray.push(fromNamesFields[i].textContent)
    }
    let fromNames = fromNameArray.join("||") // From Names

    // Subjects
    var subjectsField   = fields.subjects // prettier-ignore
    let subjectArray    = [] // prettier-ignore

    for (let i = 0; i < subjectsField.length; i++) {
        subjectArray.push(subjectsField[i].textContent)
    }
    let subjects = subjectArray.join("||") // Subjects

    // Creative
    let creatives = getCreatives()
    let creativeArray = []

    creatives.forEach((creative) => {
        creativeArray.push(creative.creative)
    })
    creatives = creativeArray.join("||||") // Creatives

    const offerID           = fields.offerID.value // prettier-ignore
    const offerName         = fields.offerName.value // prettier-ignore
    const servers           = serversArray.join(",") // prettier-ignore
    const pauseAfterSend    = fields.pauseAfterSend.value.replaceAll("\n", "||") // prettier-ignore
    const rotationAfter     = fields.rotationAfter.value // prettier-ignore
    const BCCnumber         = fields.BCCnumber.value // prettier-ignore
    const header            = fields.headers.value // prettier-ignore
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
    const countryID         = fields.countryID.value // prettier-ignore

    let documentObject = {
        offerID: offerID,
        offerName: offerName,
        servers: servers,
        pauseAfterSend: pauseAfterSend,
        rotationAfter: rotationAfter,
        BCCnumber: BCCnumber,
        header: header,
        contentType: contentType,
        charset: charset,
        encoding: encoding,
        priority: priority,
        fromNameEncoding: fromNameEncoding,
        fromName: fromNames,
        subjectEncoding: subjectEncoding,
        subject: subjects,
        fromEmailCheck: fromEmailCheck,
        fromEmail: fromEmail,
        replyToCheck: replyToCheck,
        replyTo: replyTo,
        returnPathCheck: returnPathCheck,
        returnPath: returnPath,
        link: link,
        creative: creatives,
        countryID: countryID,
    }

    const documentDataDialogue = document.getElementById("documentDataDialogue")
    const documentDataTextarea = document.getElementById("documentData")

    documentDataDialogue.classList.remove("invisible")

    documentDataTextarea.value = JSON.stringify(documentObject, null, 4)
    documentDataTextarea.select()

    console.log(documentObject)
}

function saveDocumentData() {
    const documentData = document.getElementById("documentData").value
    const closeDocumentDataDialogueButton = document.getElementById("closeDocumentDataDialogue")

    try {
        JSON.parse(documentData)
    } catch (error) {
        alert("Invalid Data")
        return
    }

    const documentObject = JSON.parse(documentData)
    organizeData(documentObject)

    closeDocumentDataDialogueButton.click()
}

function closeDataDialogue() {
    const documentDataDialogue = document.getElementById("documentDataDialogue")
    documentDataDialogue.classList.add("invisible")
}

/**
 * This function generates a random UID
 * @returns {string} UID
 */
function generateUID() {
    return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
}

/**
 * This function saves the subcreatives to the database
 * @returns {void}
 */
function saveSubcreatives(creativeArray, UID, subData) {
    console.log(creativeArray)

    creativeArray.forEach((creativeObject, key) => {
        const creative = creativeObject.creative
        const subcreatives = creativeObject.subcreatives

        subcreatives.forEach(async (subcreativeObject) => {
            const subcreative = subcreativeObject.subcreative
            const attachement = subcreativeObject.attachement

            let formData = new FormData()
            formData.append("creativeID", key)
            formData.append("UID", UID)
            formData.append(`subcreative`, subcreative)
            formData.append(`subAttachement`, attachement)
            formData.append(`subData`, JSON.stringify(subData))

            await $.ajax({
                type: "POST",
                url: "https://45.145.6.18/database/subcreatives/operations.php",
                data: formData,
                contentType: false, // Set contentType to false, as FormData already sets it to 'multipart/form-data'
                processData: false, // Set processData to false, as FormData already processes the data
                success: function (response) {
                    console.log(response)
                },
            })
        })
    })
}

function collapseSubcreatives(event) {
    const target = event
    const subcreativesContainer = target.nextElementSibling
    const collapsedAttribute = subcreativesContainer.getAttribute("data-collapsed")

    const rightArrow = target.children[0]
    const leftArrow = target.children[1]

    switch (collapsedAttribute) {
        case "true":
            subcreativesContainer.classList.remove("d-none")
            subcreativesContainer.setAttribute("data-collapsed", "false")

            target.style.width = "1.3rem"

            rightArrow.classList.remove("d-none")
            leftArrow.classList.add("d-none")
            break

        case "false":
            subcreativesContainer.classList.add("d-none")
            subcreativesContainer.setAttribute("data-collapsed", "true")

            target.style.width = "20px"

            rightArrow.classList.add("d-none")
            leftArrow.classList.remove("d-none")
            break
        default:
            break
    }
}
