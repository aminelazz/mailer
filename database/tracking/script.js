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

function sortTracking(event) {
    const target = event
    const sortingCol = target.getAttribute('data-sort-col') // Get the sorting column

    const currrentSVG = target.querySelector('svg:not(.d-none)') // Get the current sorting icon

    const nextSvg = currrentSVG.nextElementSibling || target.firstElementChild // Get the next sorting icon or the default sorting icon
    const sortMethod = nextSvg.getAttribute('data-sort') // Get the requested sorting method
    // console.log(`Current sorting: ${sortMethod}`)

    const sortingContainers = document.querySelectorAll(`.sorting-container`) // Get all sorting containers

    // Reset all sorting
    sortingContainers.forEach((container) => {
        // console.log(container)
        const svgs = container.querySelectorAll('svg')
        svgs.forEach((svg) => {
            svg.classList.add('d-none')
        })

        // Show the default sorting icon if the container is not the target
        if (container != target) {
            container.children[0].classList.remove('d-none')
        }
    })

    // Hide the current icon and show the next icon or the default icon
    currrentSVG.classList.add('d-none')
    nextSvg.classList.remove('d-none')

    // Sort the data
    // Slice the "data" array to prevent it from being modified
    const sortedData = data.slice().sort((a, b) => {
        const aData = a[sortingCol]
        const bData = b[sortingCol]

        if (sortMethod == 'asc') {
            if (sortingCol == 'date') {
                return new Date(aData) - new Date(bData) // Sort date ascending
            } else {
                return parseInt(aData) - parseInt(bData) // Sort openers acsending
            }
        } else if (sortMethod == 'desc') {
            if (sortingCol == 'date') {
                return new Date(bData) - new Date(aData) // Sort date descending
            } else {
                return parseInt(bData) - parseInt(aData) // Sort openers descending
            }
        } else {
            return 0
        }
    })

    if (sortMethod == 'default') {
        displayTracking(data) // Display the original data
    } else {
        displayTracking(sortedData) // Display the sorted data
    }
}
