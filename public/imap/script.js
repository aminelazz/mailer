var form = document.getElementById('form')
var formData = new FormData()

const targetTime = new Date()

const statusSpan = document.getElementById('status')
const statusLoading = document.getElementById('statusLoading')
const statusDone = document.getElementById('statusDone')
const statusStopped = document.getElementById('statusStopped')
const statusLabel = document.getElementById('statusLabel')
const emailsIframe = document.getElementById('emails')
const readyDiv = document.getElementById('readyDiv')

var bouncedEmailsArray = []

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))

function getBounced(event) {
    event.preventDefault()

    emailsIframe.src = ''

    statusSpan.classList.remove('d-none')

    statusLoading.classList.remove('d-none')
    statusDone.classList.add('d-none')
    statusStopped.classList.add('d-none')
    statusLabel.className = ''
    statusLabel.textContent = 'Getting bounced email addresses...'

    readyDiv.classList.add('d-none')
    emailsIframe.classList.remove('d-none')

    let server = document.getElementById('server').value
    let port = document.getElementById('port').value
    let username = document.getElementById('username').value
    let password = document.getElementById('password').value

    server = encodeURI(server)
    port = encodeURI(port)
    username = encodeURI(username)
    server = encodeURI(server)

    formData = new FormData(form)

    // emailsIframe.src = `get_emails copy.php?server=${server}&port=${port}&username=${username}&password=${password}`
    emailsIframe.src = `get_emails.php?server=${server}&port=${port}&username=${username}&password=${password}`

    emailsIframe.addEventListener('load', () => {
        statusLoading.classList.add('d-none')
        statusDone.classList.remove('d-none')
        statusLabel.className = 'text-success'
        statusLabel.textContent = 'Done'

        countdown()
    })
}

function stopExecution() {
    window.stop()

    statusLoading.classList.add('d-none')
    statusDone.classList.add('d-none')
    statusStopped.classList.remove('d-none')
    statusLabel.className = 'text-danger'
    statusLabel.textContent = 'Stopped'

    countdown()
}

function downloadBounced() {
    var emailRows = emailsIframe.contentWindow.document.querySelectorAll('.emailRow')
    const bouncedEmailsArray = []

    if (emailRows.length === 0) {
        alert('Please search for bounced email addresses first.')
        return
    } else {
        emailRows.forEach((emailRow) => {
            let checkedRow = emailRow.querySelector('.select-email').checked
            if (checkedRow) {
                bouncedEmailsArray.push(emailRow.querySelector('.bouncedEmails').textContent)
            }
        })

        if (bouncedEmailsArray.length === 0) {
            alert('Please select at least one email address.')
            return
        }

        // console.log(bouncedEmailsArray)

        const bouncedEmailsString = bouncedEmailsArray.join('\n')
        const blob = new Blob([bouncedEmailsString], { type: 'text/plain;charset=utf-8' })

        const currentDateTime = getCurrentDateTime()
        console.log(currentDateTime)
        saveAs(blob, `bounced_${currentDateTime}.txt`)
    }
}

function getCurrentDateTime() {
    const now = new Date()

    const year = now.getFullYear()
    const month = String(now.getMonth() + 1).padStart(2, '0') // Months are zero-based
    const day = String(now.getDate()).padStart(2, '0')
    const hours = String(now.getHours()).padStart(2, '0')
    const minutes = String(now.getMinutes()).padStart(2, '0')
    const seconds = String(now.getSeconds()).padStart(2, '0')

    const formattedDate = `${day}_${month}_${year}_${hours}_${minutes}_${seconds}`

    return formattedDate
}

function saveAs(blob, filename) {
    if (window.navigator.msSaveOrOpenBlob) {
        window.navigator.msSaveBlob(blob, filename)
    } else {
        const a = document.createElement('a')
        const url = window.URL.createObjectURL(blob)
        a.href = url
        a.download = filename
        a.click()
        setTimeout(() => {
            window.URL.revokeObjectURL(url)
        }, 0)

        a.remove()
    }
}

function deleteBounced(deleteButton) {
    var emailRows = emailsIframe.contentWindow.document.querySelectorAll('.emailRow')
    const bouncedEmailsUIDArray = []

    if (emailRows.length === 0) {
        alert('Please search for bounced email addresses first.')
        return
    } else {
        emailRows.forEach((emailRow) => {
            let checkedRow = emailRow.querySelector('.select-email').checked
            if (checkedRow) {
                bouncedEmailsUIDArray.push(emailRow.querySelector('.messageUID').textContent)
            }
        })

        if (bouncedEmailsUIDArray.length === 0) {
            alert('Please select at least one email address.')
            return // stop execution
        }

        let confirmation = confirm('Are you sure you want to delete the selected emails?')
        if (!confirmation) {
            return // stop execution
        }

        deleteButton.disabled = true
        deleteButton.children[0].classList.add('d-none')
        deleteButton.children[1].classList.remove('d-none')

        const bouncedEmailsUIDString = bouncedEmailsUIDArray.join(',')

        // console.log(bouncedEmailsArray)
        // create formData object containg the bouncedEmailsUIDArray
        formData.append('messageUIDs', bouncedEmailsUIDString)
        console.log(formData)

        $.ajax({
            type: 'POST',
            url: 'get_emails.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log(response)
                alert(`${response.status}\n\n${response.message}`)

                deleteButton.disabled = false
                deleteButton.children[0].classList.remove('d-none')
                deleteButton.children[1].classList.add('d-none')

                emailRows.forEach((emailRow) => {
                    let checkedRow = emailRow.querySelector('.select-email').checked
                    if (checkedRow) {
                        emailRow.remove()
                    }
                })

                const tooltip = bootstrap.Tooltip.getInstance('#deleteButton') // Returns a Bootstrap tooltip instance
                tooltip.hide()
            },
        })
    }
}
function bodyKeyDown(event) {
    if (event.key === 'Escape') {
        stopExecution()
    }
}

function selectElements(event) {
    const target = event.target
    const checkboxs = document.querySelectorAll('input[type="checkbox"]')

    if (target.checked) {
        checkboxs.forEach((checkbox) => (checkbox.checked = true))
    } else {
        checkboxs.forEach((checkbox) => (checkbox.checked = false))
    }
}

function updateCheckboxs() {
    const overall = document.getElementById('select-all')
    const checkboxs = document.querySelectorAll('.select-email')

    let checkedCount = 0
    for (const checkbox of checkboxs) {
        if (checkbox.checked) {
            checkedCount++
        }
    }

    if (checkedCount === 0) {
        overall.checked = false
        overall.indeterminate = false
    } else if (checkedCount === checkboxs.length) {
        overall.checked = true
        overall.indeterminate = false
    } else {
        overall.checked = false
        overall.indeterminate = true
    }
}

// Set the target time (1 hour from now)
function countdown() {
    // Reset the target time
    targetTime.setTime(Date.now())

    targetTime.setHours(targetTime.getHours() + 1)

    function updateCountdown() {
        const currentTime = new Date()
        const timeDifference = targetTime - currentTime

        if (timeDifference <= 0) {
            // Timer has reached zero or went negative (1 hour passed)
            clearInterval(timer)
            document.getElementById('countdown').textContent = 'Countdown expired!'
        } else {
            // Calculate remaining time
            // 1 hour = 3600000 milliseconds
            const hours = Math.floor(timeDifference / 3600000).toString().padStart(2, '0') //prettier-ignore
            // 1 minute = 60000 milliseconds
            const minutes = Math.floor((timeDifference % 3600000) / 60000).toString().padStart(2, '0') //prettier-ignore
            // 1 second = 1000 milliseconds
            const seconds = Math.floor((timeDifference % 60000) / 1000).toString().padStart(2, '0') //prettier-ignore

            // Display the remaining time
            document.getElementById('countdown').textContent = `${hours}:${minutes}:${seconds}`
        }
    }

    // Update the countdown every second
    const timer = setInterval(updateCountdown, 1000)

    // Initial update
    updateCountdown()
}
