var data
const notFound = document.getElementById('notFound')
const loadingSpinner = document.getElementById('loadingSpinner')
const error = document.getElementById('error')
const downloadButton = document.getElementById('downloadButton')

window.onload = getTracking()
window.onload = changeSearchType()

var selectAll = document.getElementById('select-all')
selectAll.checked = false

var tr = document.getElementById('rowExample')
var rowExample = tr.cloneNode(true)
tr.remove()

async function getTracking() {
    notFound.style.display = 'none'
    error.style.display = 'none'
    loadingSpinner.style.display = 'flex'

    const result = await fetch('./php/get_tracking.php')
    data = await result.json()

    loadingSpinner.style.display = 'none'

    let searchTerm = document.getElementById('search')
    searchTerm.value = ''

    if (data.status) {
        error.style.display = 'flex'
        error.querySelector('.message').textContent = data.message
        return
    }

    if (data.length == 0) {
        document.getElementById('notFound').style.display = 'flex'
        return
    }

    // console.log(data)
    displayTracking(data)
}

function displayTracking(data) {
    if (data.length == 0) {
        document.getElementById('notFound').style.display = 'flex'
    } else {
        document.getElementById('notFound').style.display = 'none'
    }

    const tableBody = document.getElementById('tableBody')
    tableBody.innerHTML = ''

    data.forEach((tracking) => {
        let row = rowExample.cloneNode(true)
        row.removeAttribute('id')
        row.classList.remove('d-none')

        row.setAttribute('data-offer', tracking.offer)
        row.setAttribute('data-country', tracking.country)
        row.setAttribute('data-countryID', tracking.countryID)
        row.setAttribute('data-date', tracking.date)

        row.children[0].innerHTML = tracking.offer
        row.children[1].innerHTML = tracking.country
        row.children[2].innerHTML = tracking.openers
        row.children[3].innerHTML = tracking.date

        tableBody.appendChild(row)
    })
}

function changeSearchType() {
    const trackingAttr = document.getElementById('trackingAttr').value
    let searchField = document.getElementById('search')

    searchField.value = ''

    if (trackingAttr == 'date') {
        searchField.type = 'date'
    } else {
        searchField.type = 'search'
    }
}

function searchTracking() {
    let searchTerm = document.getElementById('search').value
    const trackingAttr = document.getElementById('trackingAttr').value

    // filter data
    const filteredData = data.filter((tracking) => {
        return tracking[trackingAttr].toLowerCase().includes(searchTerm.toLowerCase())
    })

    displayTracking(filteredData)
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
    const checkboxs = document.querySelectorAll('.select-opener')

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

async function downloadOpeners() {
    const checkboxs = document.querySelectorAll('.select-opener')

    downloadButton.disabled = true
    downloadButton.children[0].classList.add('d-none')
    downloadButton.children[1].classList.remove('d-none')

    let openers = 0
    let openerArray = [] // Create an array to hold all the opener arrays

    for (const checkbox of checkboxs) {
        if (checkbox.checked) {
            openers++
            let opener = {}
            let row = checkbox.parentElement.parentElement

            opener.offer = row.getAttribute('data-offer')
            opener.country = row.getAttribute('data-country')
            opener.countryID = row.getAttribute('data-countryID')
            opener.date = row.getAttribute('data-date')

            openerArray.push(opener) // Push the opener array to the openerArray
        }
    }

    if (openers == 0) {
        alert('Please select at least one offer')
    } else {
        const formData = new FormData()
        formData.append('openers', JSON.stringify(openerArray)) // Append the JSON-encoded array to the form data
        // console.log(formData)

        await $.ajax({
            type: 'POST',
            url: './php/download_openers.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                response = JSON.parse(response)
                // console.log(response.data)

                let emails = response.data.join('\n')
                let blob = new Blob([emails], { type: 'text/plain;charset=utf-8' })
                saveAs(blob, 'openers.txt')
            },
            error: function (response) {
                console.log(response)
            },
        })
    }

    downloadButton.disabled = false
    downloadButton.children[0].classList.remove('d-none')
    downloadButton.children[1].classList.add('d-none')
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
