<?php
date_default_timezone_set('Africa/Casablanca');
?>
<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" href="./favicon.ico" type="image/x-icon">
   <link rel="stylesheet" href="./public/stylesheets/bootstrap_mod.css">
   <link rel="stylesheet" href="./public/stylesheets/styles.css">
   <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20,300,0,-25" />
   <!-- include summernote css-->
   <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
   <script src="./public/js/auth.js"></script>
   <title>St-Com</title>
</head>

<body class="row w-100 h-100" style="background-color: #E2E2E2;">
   <form action="public/functions/send_email.php" method="post" class="p-0" id="sendForm" enctype="multipart/form-data">
      <!-- Start Side panel -->
      <div class="col p-0 position-fixed start-0 h-100" style="max-width: 220px; background-color: #8fa1a3; overflow: auto;">
         <div class="m-2 ">
            <center>
               <a href="">
                  <img src="./public/assets/logo.jpg" alt="St-com" width="40%" class="rounded-circle">
               </a>
            </center>
         </div>
         <!-- Navigation Buttons -->
         <div class="row m-auto">
            <a href="#Servers" class="btn py-3 px-0 fw-semibold text-white rounded-0 navigation">
               <div class="d-flex flex-row justify-content-between px-4">
                  <div>Servers</div>
                  <img src="./public/assets/servers.svg" alt="">
               </div>
            </a>
            <a href="#Header" class="btn py-3 px-0 fw-semibold text-white rounded-0 navigation">
               <div class="d-flex flex-row justify-content-between px-4">
                  <div>Header</div>
                  <img src="./public/assets/header.svg" alt="">
               </div>
            </a>
            <a href="#Tools" class="btn py-3 px-0 fw-semibold text-white rounded-0 navigation">
               <div class="d-flex flex-row justify-content-between px-4">
                  <div>Tools</div>
                  <img src="./public/assets/tools.svg" alt="">
               </div>
            </a>
            <a href="#Properties" class="btn py-3 px-0 fw-semibold text-white rounded-0 navigation">
               <div class="d-flex flex-row justify-content-between px-4">
                  <div>Properties</div>
                  <img src="./public/assets/properties.svg" alt="">
               </div>
            </a>
            <a href="#Body" class="btn py-3 px-0 fw-semibold text-white rounded-0 navigation">
               <div class="d-flex flex-row justify-content-between px-4">
                  <div>Body</div>
                  <img src="./public/assets/body.svg" alt="">
               </div>
            </a>
            <a href="#Recipients" class="btn py-3 px-0 fw-semibold text-white rounded-0 navigation">
               <div class="d-flex flex-row justify-content-between px-4">
                  <div>Recipients</div>
                  <img src="./public/assets/recepients.svg" alt="">
               </div>
            </a>
            <a href="#Result" class="btn py-3 px-0 fw-semibold text-white rounded-0 navigation">
               <div class="d-flex flex-row justify-content-between px-4">
                  <div>Result</div>
                  <img src="./public/assets/status.svg" alt="">
               </div>
            </a>
            <!-- Start Button -->
            <button id="startSend" type="submit" class="send btn btn-success bord-0 my-2 mx-auto text-white fw-semibold" style="height: 43px; width: 85%;" onclick="checkFields()">Start</button>
            <!-- Play & Pause & Stop Buttons -->
            <div id="controlArea" class="d-flex justify-content-evenly mx-auto h-auto p-0 invisible" style="width: 85%;">
               <button id="play" type="button" class="col-3 btn btn-play border-2" onclick="send()">
                  <img src="./public/assets/play.svg" alt="Play" class=" pb-1">
               </button>
               <button id="pause" type="button" class="col-3 btn btn-warning border-2" onclick="pauseSend()">
                  <img src="./public/assets/pause.svg" alt="Pause" class=" pb-1">
               </button>
               <button id="stop" type="button" class="col-3 btn btn-danger border-2" onclick="stopSend()">
                  <img src="./public/assets/stop.svg" alt="Cancel" class=" pb-1">
               </button>
            </div>
            <!-- Status Label -->
            <div class="text-white text-center fw-semibold my-2" id="status">Status: Pending</div>
            <!-- Progress bar & Send count -->
            <div>
               <div class="progress p-0 mx-auto invisible" id="progressBarContainer" role="progressbar" style="width: 85%;" aria-label="Sent emails" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar bg-play" id="progressBar" style="width: 0%">0%</div>
               </div>
               <div id="sendCount" class="text-white text-center fw-semibold my-1" style="font-size: 0.85rem;"></div>
            </div>
            <!-- Schedule -->
            <div class="text-white text-center fw-semibold my-3" id="status">Schedule</div>
            <div class="d-flex flex-column gap-2 py-2">
               <input class="form-control" type="datetime-local" value="<?php echo date('Y-m-d\TH:i') ?>" name="schedule" id="schedule" style="font-size: 0.9rem;">
               <div class="d-flex gap-2" style="height: 43px;">
                  <button id="cancelSchedule" class="btn btn-secondary p-0 px-2 d-none" type="button" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Cancel Schedule">
                     <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="white" class="bi bi-stop-fill" viewBox="0 0 16 16">
                        <path d="M5 3.5h6A1.5 1.5 0 0 1 12.5 5v6a1.5 1.5 0 0 1-1.5 1.5H5A1.5 1.5 0 0 1 3.5 11V5A1.5 1.5 0 0 1 5 3.5z"></path>
                     </svg>
                  </button>
                  <button class="btn btn-play fw-semibold flex-fill" type="button" onclick="setSchedule(this)">Set</button>
               </div>
            </div>
         </div>
      </div>
      <!-- End Side Panel -->
      <!-- Start Main Menu -->
      <div class="main-menu d-flex h-100">
         <div style="width: 250px"></div>
         <div class="col mb-4 container-xxl" style="padding-top: 10px; position: relative;">
            <div class="d-flex align-items-center justify-content-between">
               <div class="d-flex gap-4 align-items-center">
                  <h4>St-Com Mailing</h4>
                  <h6>Ver. 16.3</h6>
               </div>
               <!-- Welcome & Logout -->
               <div class="btn-group">
                  <button type="button" class="btn">Welcome, <span id="mailerName" class="fw-semibold"></span></button>
                  <button type="button" class="btn dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                     <span class="visually-hidden">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu  dropdown-menu-end">
                     <li>
                        <button type="button" class="btn btn-outline-danger dropdown-item" onclick="logout()">Log out</button>
                     </li>
                  </ul>
               </div>

            </div>
            <hr>
            <!-- Tabs -->
            <ul class="nav nav-tabs" id="nav-tab" role="tablist">
               <li class=" nav-item" role="presentation">
                  <button class="nav-link active" id="nav-send-tab" data-bs-toggle="tab" data-bs-target="#nav-send" type="button" role="tab" aria-controls="nav-send" aria-selected="true" onclick="hideRefresh()">Send</button>
               </li>
               <li class="nav-item" role="presentation">
                  <button class="nav-link" id="nav-history-tab" data-bs-toggle="tab" data-bs-target="#nav-history" type="button" role="tab" aria-controls="nav-history" aria-selected="false" onclick="showRefresh()">History</button>
               </li>
               <li class="nav-item" role="presentation">
                  <button class="nav-link" id="nav-data-tab" data-bs-toggle="tab" data-bs-target="#nav-data" type="button" role="tab" aria-controls="nav-data" aria-selected="false" onclick="hideRefresh()">Data</button>
               </li>
               <li class="nav-item" role="presentation">
                  <button class="nav-link" id="nav-imap-tab" data-bs-toggle="tab" data-bs-target="#nav-imap" type="button" role="tab" aria-controls="nav-imap" aria-selected="false" onclick="hideRefresh()">Imap Checker</button>
               </li>
               <li class="nav-item" role="presentation">
                  <button class="nav-link" id="nav-tracking-tab" data-bs-toggle="tab" data-bs-target="#nav-tracking" type="button" role="tab" aria-controls="nav-tracking" aria-selected="false" onclick="hideRefresh()">Tracking</button>
               </li>
               <li class="nav-item me-auto" role="presentation">
                  <button class="nav-link" id="nav-smtp-check-tab" data-bs-toggle="tab" data-bs-target="#nav-smtp-check" type="button" role="tab" aria-controls="nav-smtp-check" aria-selected="false" onclick="hideRefresh()">SMTP check</button>
               </li>
               <div class="me-2 invisible" id="refreshDiv">
                  <button class="my-auto btn btn-primary border-0 d-flex gap-1 align-items-center refresh-dropbox" type="button" onclick="refreshIframe()">
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
                        <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"></path>
                        <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"></path>
                     </svg>
                     Refresh
                  </button>
               </div>
            </ul>
            <!-- Content -->
            <div class="tab-content pt-2" style="height: 90%;">
               <!-- Send -->
               <div class="tab-pane active" id="nav-send" role="tabpanel" aria-labelledby="nav-send-tab" tabindex="0">
                  <!-- Start Servers -->
                  <div id="Servers" class="card">
                     <h5 class="card-header fw-semibold">
                        Servers Section
                     </h5>
                     <div class="card-body border-3 border-top border-success" style="background-color: #E2E2E2;">
                        <div class="row">
                           <!-- Start Server Field -->
                           <div class="col">
                              <label for="servers" class="form-label fw-semibold">
                                 Servers
                              </label>
                              <div class="mb-2 d-flex gap-2" style="height: 43px;">
                                 <input spellcheck="false" name="servers" id="servers" class="form-control" pattern="/^(?:[\w.-]+:\d+:(?:tls|ssl|):[\w.-]+@[\w.-]+:\S+)$/gm" style="font-size: 0.85rem;" placeholder="Host:Port:Encryption:User:Pass" onkeydown="addServerToken(event)"></input>
                                 <!-- Add server token button -->
                                 <button type="button" class="btn btn-secondary h-100 d-flex align-items-center" onclick="addServerToken({ key: 'Enter', target: this.previousElementSibling, preventDefault: () => {}})">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="26" viewBox="0 -960 960 960" width="26" fill="currentColor">
                                       <path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z" />
                                    </svg>
                                 </button>
                              </div>
                              <div spellcheck="false" id="serverContainer" class="servers form-control" pattern="/^(?:[\w.-]+:\d+:(?:tls|ssl):[\w.-]+@[\w.-]+:\S+)$/gm" style="height: 200px; max-width: 100%; font-size: 0.95rem; overflow: auto;" data-placeholder="Host:Port:Encryption:User:Pass" onpaste="pasteServers(event)">
                                 <div id="serverTokenExample" class="server-token d-none">
                                    <div class="server-nbr">N</div>
                                    <div class="label">smtp:port:encryption:</div>
                                    <div role="button" onclick="this.parentNode.remove()">
                                       <!-- Cross Icon (Remove) -->
                                       <svg xmlns="http://www.w3.org/2000/svg" height="18" viewBox="0 -960 960 960" width="18">
                                          <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z" />
                                       </svg>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <!-- End Server Field -->
                           <div class="col">
                              <div class="row pb-3">
                                 <div class="col">
                                    <label for="pauseAfterSend" class="form-label fw-semibold">
                                       Pause After Send <span class="fst-italic">(Seconds)</span>
                                    </label>
                                    <input type="number" name="pauseAfterSend" id="pauseAfterSend" class="form-control" style="height: 43px;" value="2" min="0">
                                 </div>
                                 <div class=" col">
                                    <label for="rotationAfter" class="form-label fw-semibold">
                                       Rotation After <span class="fst-italic">(Seconds)</span>
                                    </label>
                                    <input type="number" name="rotationAfter" id="rotationAfter" class="form-control" style="height: 43px;" value="1" min="0" onchange="checkFields()">
                                 </div>
                              </div>
                              <div class=" row">
                                 <!-- <div class="col">
                                    <label for="emailPerSecond" class="form-label fw-semibold">
                                       Email per second
                                    </label>
                                 <input type="number" name="emailPerSecond" id="emailPerSecond" class="form-control" style="height: 43px;" value="1" min="1">
                                 </div> -->
                                 <div class="col">
                                    <label for="BCCnumber" class="form-label fw-semibold">
                                       Number of Emails In Bcc
                                    </label>
                                    <input type="number" name="BCCnumber" id="BCCnumber" class="form-control" style="height: 43px;" value="1" min="1" max="10">
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- End Servers -->

                  <hr class=" my-4">

                  <!-- Start Header -->
                  <div id="Header" class="card">
                     <h5 class="card-header fw-semibold">
                        Header Section
                     </h5>
                     <div class="card-body border-3 border-top border-success" style="background-color: #E2E2E2;">
                        <div class="row">
                           <!-- Custom Header -->
                           <div class="col">
                              <label for="headers" class="form-label fw-semibold">
                                 Custom Header
                              </label>
                              <textarea spellcheck="false" name="headers" id="headers" class="form-control" style="height: 134px; resize: none; font-size: 0.85rem;">Message-ID: <[anl_20]@[domain]>&#10;X-Mailer: St-Com v1.0-Ref[n_2]&#10;Auto-Submitted: auto-generated&#10;X-Auto-Response-Suppress: OOF, AutoReply&#10;X-Abuse: Please report abuse here <mailto:abuse@[domain]?c=[n_10]></textarea>
                           </div>
                           <!-- Message Properties -->
                           <div class="col">
                              <!-- Content Type & Charset -->
                              <div class="row pb-3">
                                 <!-- Content Type -->
                                 <div class="col">
                                    <label for="contentType" class="form-label fw-semibold">
                                       Content Type
                                    </label>
                                    <select name="contentType" id="contentType" class="form-select" style="height: 43px;">
                                       <option value="text/html">
                                          text/html
                                       </option>
                                       <option value="text/plain">
                                          text/plain
                                       </option>
                                       <option value="multipart/alternative">
                                          multipart/alternative
                                       </option>
                                    </select>
                                 </div>
                                 <!-- Charset -->
                                 <div class="col">
                                    <label for="charset" class="form-label fw-semibold">
                                       Charset
                                    </label>
                                    <select name="charset" id="charset" class="form-select" style="height: 43px;">
                                       <option value="UTF-8">
                                          UTF-8
                                       </option>
                                       <option value="US-ASCII">
                                          US-ASCII
                                       </option>
                                       <option value="ISO-8859-1">
                                          ISO-8859-1
                                       </option>
                                    </select>
                                 </div>
                              </div>
                              <!-- Content-Transfer-Encoding & Priority -->
                              <div class="row">
                                 <!-- Content-Transfer-Encoding -->
                                 <div class="col">
                                    <label for="encoding" class="form-label fw-semibold">
                                       Content-Transfer-Encoding
                                    </label>
                                    <select name="encoding" id="encoding" class="form-select" style="height: 43px;">
                                       <option value="7bit">
                                          7bit
                                       </option>
                                       <option value="8bit">
                                          8bit
                                       </option>
                                       <option value="base64">
                                          Base64
                                       </option>
                                       <option value="binary">
                                          Binary
                                       </option>
                                       <option value="quoted-printable">
                                          Quoted-Printable
                                       </option>
                                    </select>
                                 </div>
                                 <!-- Priority -->
                                 <div class="col">
                                    <label for="priority" class="form-label fw-semibold">
                                       Priority
                                    </label>
                                    <select name="priority" id="priority" class="form-select" style="height: 43px;">
                                       <option value="">
                                          Default
                                       </option>
                                       <option value="5">
                                          Low
                                       </option>
                                       <option value="3" selected>
                                          Normal
                                       </option>
                                       <option value="1">
                                          High
                                       </option>
                                    </select>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- End Header -->

                  <hr class="my-4">

                  <!-- Start Tools -->
                  <div id="Tools" class="card">
                     <h5 class="card-header fw-semibold">
                        Tools Section
                     </h5>
                     <div class="card-body border-3 border-top border-success" style="background-color: #E2E2E2;">
                        <div class="row">
                           <!-- Left Side -->
                           <div class="col">
                              <!-- Generator -->
                              <div class="row mx-0">
                                 <div class="col pb-3 p-0">
                                    <label class="form-label fw-semibold" for="length">
                                       Generator Settings
                                    </label>
                                    <div class="row flex-nowrap mx-0">
                                       <div class="col-9">
                                          <div class="row flex-nowrap align-items-center justify-content-around border rounded-2 rounded-end-0 w-auto p-0" style="height: 43px; background-color: white;">
                                             <div class="w-auto px-0">
                                                <div class="form-check form-switch">
                                                   <input class="form-check-input" id="AZ" type="checkbox" role="switch" value="AZ">
                                                   <label class="form-check-label" for="AZ">Range A-Z</label>
                                                </div>
                                             </div>
                                             <div class="w-auto px-0">
                                                <div class="form-check form-switch">
                                                   <input class="form-check-input" id="az" type="checkbox" role="switch" value="az">
                                                   <label class="form-check-label" for="az">Range a-z</label>
                                                </div>
                                             </div>
                                             <div class="w-auto px-0">
                                                <div class="form-check form-switch">
                                                   <input class="form-check-input" id="09" type="checkbox" role="switch" value="09">
                                                   <label class="form-check-label" for="09">Range 0-9</label>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-3 p-0">
                                          <input type="number" name="length" id="length" class="form-control rounded-start-0" min="1" style="height: 43px; margin-left: -1px;" placeholder="Length">
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <!-- Result -->
                              <div class="col">
                                 <label class="form-label fw-semibold" for="pattern">
                                    Pattern Value
                                 </label>
                                 <div class="row mx-0">
                                    <div class="col-9 p-0">
                                       <input type="text" class="form-control rounded-end-0" name="pattern" id="pattern" style="height: 43px;" placeholder="Pattern" disabled>
                                    </div>
                                    <div class="col-3 p-0">
                                       <button type="button" class="send btn btn-success w-100 rounded-start-0" id="pBtn" style="height: 43px;" onclick="generatePattern()">
                                          Get Pattern
                                       </button>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <!-- Right Side -->
                           <div class="col">
                              <label class="form-label fw-semibold">
                                 Instruction
                              </label>
                              <div class="border rounded-2 py-2" style="background-color: #E9ECEF;">
                                 <ol>
                                    <li><span class="header">General Tags</span>
                                       <ul>
                                          <li>
                                             <span>[an_N]</span>:&nbsp;
                                             Random String Generator
                                          </li>
                                          <li>
                                             <span>[mail_date]</span>:&nbsp;
                                             Date <span class="fst-italic fw-semibold">(<?php echo date('d/m/Y') ?>)</span>
                                          </li>
                                          <li>
                                             <span>[time]</span>:&nbsp;
                                             Time <span class="fst-italic fw-semibold">(<?php echo date('H:i:s') ?>)</span>
                                          </li>
                                          <li>
                                             <span>[email]</span>:&nbsp;
                                             Reciever Email <span class="fst-italic fw-semibold">(email_user@domain.com)</span>
                                          </li>
                                          <li>
                                             <span>[first_name]</span>:&nbsp;
                                             Reciever Name <span class="fst-italic fw-semibold">(email_user)</span>
                                          </li>
                                          <li>
                                             <span>[domain]</span>:&nbsp;
                                             Server Domain
                                          </li>
                                          <li>
                                             <span>[url]</span>:&nbsp;
                                             Your Link
                                          </li>
                                       </ul>
                                    </li>
                                    <li><span class="header">Example</span>
                                       <ul>
                                          <li>
                                             Hello <span class="fst-italic fw-semibold">[user]</span>
                                             Hello <span class="fst-italic fw-semibold">user</span>
                                          </li>
                                          <li>
                                             Your Code Is <span class="fst-italic fw-semibold">[an_8]</span>
                                             Your Code Is <span class="fst-italic fw-semibold">A4s5FhrN</span>
                                          </li>
                                       </ul>
                                    </li>
                                 </ol>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- End Tools -->

                  <hr class="my-4">

                  <!-- Start Properties -->
                  <div id="Properties" class="card">
                     <h5 class="card-header fw-semibold">
                        Properties Section
                     </h5>
                     <div class="card-body border-3 border-top border-success" style="background-color: #E2E2E2;">
                        <!-- Offer Infos & Country -->
                        <div class="row pb-3">
                           <!-- Offer Infos -->
                           <div class="col-6">
                              <label for="offerInfos" class="form-label fw-semibold">
                                 Offer Infos
                              </label>
                              <div class="input-group" id="offerInfos">
                                 <input type="number" class="form-control w-25" id="offerID" name="offerID" placeholder="Offer ID" min="1" style="height: 43px;" required>
                                 <input type="text" class="form-control w-75" id="offerName" name="offerName" placeholder="Offer Name" style="height: 43px;" required>
                              </div>
                           </div>
                           <!-- Country -->
                           <div class="col-6">
                              <label for="country" class="form-label fw-semibold">
                                 Country
                              </label>
                              <select class="form-select" id="country" name="country" aria-placeholder="Choose a country..." placeholder="Choose a country..." style="height: 43px;" required>
                                 <option value="" selected hidden style="color: #595C66;">Choose a country...</option>
                                 <!-- Europe Countries -->
                                 <optgroup label="Europe">
                                    <option value="1">Albania</option>
                                    <option value="2">Andorra</option>
                                    <option value="3">Austria</option>
                                    <option value="4">Belarus</option>
                                    <option value="5">Belgium</option>
                                    <option value="6">Bosnia and Herzegovina</option>
                                    <option value="7">Bulgaria</option>
                                    <option value="8">Croatia</option>
                                    <option value="9">Cyprus</option>
                                    <option value="10">Czech Republic</option>
                                    <option value="11">Denmark</option>
                                    <option value="12">Estonia</option>
                                    <option value="13">Faroe Islands</option>
                                    <option value="14">Finland</option>
                                    <option value="15">France</option>
                                    <option value="16">Germany</option>
                                    <option value="17">Gibraltar</option>
                                    <option value="18">Greece</option>
                                    <option value="19">Hungary</option>
                                    <option value="20">Iceland</option>
                                    <option value="21">Ireland</option>
                                    <option value="22">Isle of Man</option>
                                    <option value="23">Italy</option>
                                    <option value="24">Latvia</option>
                                    <option value="25">Liechtenstein</option>
                                    <option value="26">Lithuania</option>
                                    <option value="27">Luxembourg</option>
                                    <option value="28">Macedonia</option>
                                    <option value="29">Malta</option>
                                    <option value="30">Moldova</option>
                                    <option value="31">Monaco</option>
                                    <option value="32">Montenegro</option>
                                    <option value="33">Netherlands</option>
                                    <option value="34">Norway</option>
                                    <option value="35">Poland</option>
                                    <option value="36">Portugal</option>
                                    <option value="37">Romania</option>
                                    <option value="38">Russia</option>
                                    <option value="39">San Marino</option>
                                    <option value="40">Serbia</option>
                                    <option value="41">Slovakia</option>
                                    <option value="42">Slovenia</option>
                                    <option value="43">Spain</option>
                                    <option value="44">Sweden</option>
                                    <option value="45">Switzerland</option>
                                    <option value="46">Ukraine</option>
                                    <option value="47">United Kingdom</option>
                                 </optgroup>
                                 <!-- America -->
                                 <optgroup label="America">
                                    <option value="48">United States</option>
                                    <option value="49">Canada</option>
                                 </optgroup>
                                 <!-- Australia -->
                                 <optgroup label="Australia">
                                    <option value="50">Australia</option>
                                 </optgroup>
                              </select>
                           </div>
                        </div>
                        <!-- From Name & Subject -->
                        <div class="row pb-3">
                           <!-- From Name -->
                           <div class="col-6">
                              <label for="fromName" class="form-label fw-semibold">
                                 From Name
                              </label>
                              <div class="row mx-0">
                                 <div class="col-4 p-0">
                                    <select class="form-select border-end-0 rounded-end-0" name="fromNameEncoding" id="fromNameEncoding" style="height: 43px;">
                                       <option value="">
                                          Default
                                       </option>
                                       <option value="7bit">
                                          7bit
                                       </option>
                                       <option value="base64" selected>
                                          Base64
                                       </option>
                                       <option value="binary">
                                          Binary
                                       </option>
                                       <option value="quoted-printable">
                                          Quoted-Printable
                                       </option>
                                    </select>
                                 </div>
                                 <div class="col-8 p-0">
                                    <input type="text" class="properties form-control rounded-start-0" id="fromName" name="fromName" placeholder="From Name" value="[au_10]" style="height: 43px;" onkeydown="addToken(event)">
                                 </div>
                              </div>
                           </div>
                           <!-- Subject -->
                           <div class="col-6">
                              <label for="subject" class="form-label fw-semibold">
                                 Subject
                              </label>
                              <div class="row mx-0">
                                 <div class="col-4 p-0">
                                    <select class="form-select border-end-0 rounded-end-0" name="subjectEncoding" id="subjectEncoding" style="height: 43px;">
                                       <option value="">
                                          Default
                                       </option>
                                       <option value="7bit">
                                          7bit
                                       </option>
                                       <option value="base64" selected>
                                          Base64
                                       </option>
                                       <option value="binary">
                                          Binary
                                       </option>
                                       <option value="quoted-printable">
                                          Quoted-Printable
                                       </option>
                                    </select>
                                 </div>
                                 <div class="col-8 p-0">
                                    <input type="text" class="properties form-control rounded-start-0" id="subject" name="subject" placeholder="Subject" value="[au_10]" style="height: 43px;" onkeydown="addToken(event)">
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- From Names & Subjects -->
                        <div class="row pb-3">
                           <!-- From Names -->
                           <div class="col">
                              <div id="fromNamesContainer" class="d-flex" style="height: 180px;">
                                 <div id="fromNames" class="random-divs h-100 fromNames row mx-0 form-control rounded-end-0 border-end-0" data-placeholder="From Names..."></div>

                                 <div id="btnContainer" class="btnContainer flex-column ms-auto h-100" style="width: 50px;">
                                    <!-- Copy Button -->
                                    <button id="export" class="idle copy_paste btn btn-play flex-fill rounded-0 rounded-end rounded-bottom-0 border-0" type="button" onclick="copyTokens(this)">
                                       <!-- Copy Icon -->
                                       <svg xmlns="http://www.w3.org/2000/svg" height="22" viewBox="0 -960 960 960" width="22" fill="currentColor">
                                          <path d="M360-240q-33 0-56.5-23.5T280-320v-480q0-33 23.5-56.5T360-880h360q33 0 56.5 23.5T800-800v480q0 33-23.5 56.5T720-240H360ZM200-80q-33 0-56.5-23.5T120-160v-560h80v560h440v80H200Z" />
                                       </svg>
                                       <!-- Done Button -->
                                       <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor" class="d-none">
                                          <path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z" />
                                       </svg>
                                    </button>
                                    <!-- Paste Button -->
                                    <button id="import" class="idle copy_paste btn btn-play flex-fill rounded-0 border-0" type="button" onclick="pasteTokens(this)">
                                       <!-- Paste Icon -->
                                       <svg xmlns="http://www.w3.org/2000/svg" height="21" viewBox="0 -960 960 960" width="21" fill="currentColor">
                                          <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h167q11-35 43-57.5t70-22.5q40 0 71.5 22.5T594-840h166q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560h-80v120H280v-120h-80v560Zm280-560q17 0 28.5-11.5T520-800q0-17-11.5-28.5T480-840q-17 0-28.5 11.5T440-800q0 17 11.5 28.5T480-760Z" />
                                       </svg>
                                       <!-- Done Button -->
                                       <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor" class="d-none">
                                          <path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z" />
                                       </svg>
                                    </button>
                                    <!-- Edit Button -->
                                    <button id="import" class="idle edit btn btn-play flex-fill rounded-0 border-0" type="button" onclick="editTokens(this)">
                                       <!-- Edit Icon -->
                                       <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor">
                                          <path d="M772-603 602-771l56-56q23-23 56.5-23t56.5 23l56 56q23 23 24 55.5T829-660l-57 57Zm-58 59L290-120H120v-170l424-424 170 170Z" />
                                       </svg>
                                       <!-- Done Button -->
                                       <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor" class="d-none">
                                          <path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z" />
                                       </svg>
                                    </button>
                                    <!-- Clear Button -->
                                    <button id="clear" class="idle btn btn-danger flex-fill rounded-0 rounded-bottom rounded-start-0  border-0" type="button" onclick="clearTokens(this)">
                                       <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 -960 960 960" fill="currentColor">
                                          <path d="m376-300 104-104 104 104 56-56-104-104 104-104-56-56-104 104-104-104-56 56 104 104-104 104 56 56Zm-96 180q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Z" />
                                       </svg>
                                    </button>
                                 </div>
                              </div>

                              <div id="importfromNamesContainer" class="d-none" style="height: 180px;">
                                 <textarea id="importfromNames" class="h-100 row mx-0 form-control rounded-end-0 border-end-0" style="resize: none; font-family: monospace; font-size: 0.8em;" placeholder="Copy/Paste 'from names' here..."></textarea>

                                 <div class="btnContainer d-flex flex-column ms-auto h-100 bg-white" style="width: 50px;">
                                    <button id="okFromName" class="ok btn btn-success h-100 w-100 rounded-0 rounded-end border-0" type="button" onclick="okTokens(this)">
                                       <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor">
                                          <path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z" />
                                       </svg>
                                    </button>
                                 </div>
                              </div>
                           </div>

                           <!-- Subjects -->
                           <div class="col">
                              <div id="subjectsContainer" class="d-flex" style="height: 180px;">
                                 <div id="subjects" class="random-divs h-100 subjects row mx-0 form-control rounded-end-0 border-end-0" data-placeholder="Subjects..."></div>

                                 <div id="btnContainer" class="btnContainer flex-column ms-auto h-100" style="width: 50px;">
                                    <!-- Copy Button -->
                                    <button id="export" class="idle copy_paste btn btn-play flex-fill rounded-0 rounded-end rounded-bottom-0 border-0" type="button" onclick="copyTokens(this)">
                                       <!-- Copy Icon -->
                                       <svg xmlns="http://www.w3.org/2000/svg" height="22" viewBox="0 -960 960 960" width="22" fill="currentColor">
                                          <path d="M360-240q-33 0-56.5-23.5T280-320v-480q0-33 23.5-56.5T360-880h360q33 0 56.5 23.5T800-800v480q0 33-23.5 56.5T720-240H360ZM200-80q-33 0-56.5-23.5T120-160v-560h80v560h440v80H200Z" />
                                       </svg>
                                       <!-- Done Button -->
                                       <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor" class="d-none">
                                          <path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z" />
                                       </svg>
                                    </button>
                                    <!-- Paste Button -->
                                    <button id="import" class="idle copy_paste btn btn-play flex-fill rounded-0 border-0" type="button" onclick="pasteTokens(this)">
                                       <!-- Paste Icon -->
                                       <svg xmlns="http://www.w3.org/2000/svg" height="21" viewBox="0 -960 960 960" width="21" fill="currentColor">
                                          <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h167q11-35 43-57.5t70-22.5q40 0 71.5 22.5T594-840h166q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560h-80v120H280v-120h-80v560Zm280-560q17 0 28.5-11.5T520-800q0-17-11.5-28.5T480-840q-17 0-28.5 11.5T440-800q0 17 11.5 28.5T480-760Z" />
                                       </svg>
                                       <!-- Done Button -->
                                       <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor" class="d-none">
                                          <path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z" />
                                       </svg>
                                    </button>
                                    <!-- Edit Button -->
                                    <button id="import" class="idle edit btn btn-play flex-fill rounded-0 border-0" type="button" onclick="editTokens(this)">
                                       <!-- Edit Icon -->
                                       <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor">
                                          <path d="M772-603 602-771l56-56q23-23 56.5-23t56.5 23l56 56q23 23 24 55.5T829-660l-57 57Zm-58 59L290-120H120v-170l424-424 170 170Z" />
                                       </svg>
                                       <!-- Done Button -->
                                       <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor" class="d-none">
                                          <path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z" />
                                       </svg>
                                    </button>
                                    <!-- Clear Button -->
                                    <button id="clear" class="idle btn btn-danger flex-fill rounded-0 rounded-bottom rounded-start-0  border-0" type="button" onclick="clearTokens(this)">
                                       <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 -960 960 960" fill="currentColor">
                                          <path d="m376-300 104-104 104 104 56-56-104-104 104-104-56-56-104 104-104-104-56 56 104 104-104 104 56 56Zm-96 180q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Z" />
                                       </svg>
                                    </button>
                                 </div>
                              </div>

                              <div id="importsubjectsContainer" class="d-none" style="height: 180px;">
                                 <textarea id="importsubjects" class="h-100 row mx-0 form-control rounded-end-0 border-end-0" style="resize: none; font-family: monospace; font-size: 0.8em;" placeholder="Copy/Paste 'subjects' here..."></textarea>

                                 <div class="btnContainer d-flex flex-column ms-auto h-100 bg-white" style="width: 50px;">
                                    <button id="okSubject" class="ok btn btn-success h-100 w-100 rounded-0 rounded-end border-0" type="button" onclick="okTokens(this)">
                                       <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor">
                                          <path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z" />
                                       </svg>
                                    </button>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- From Email & Reply-To & Return Path -->
                        <div class="row">
                           <!-- From Email -->
                           <div class="col-4">
                              <label for="fromEmail" class="form-label fw-semibold">From Email</label>
                              <div class="row mx-0">
                                 <div class="col-4 p-0 border rounded-2 rounded-end-0 d-flex align-items-center justify-content-center" style="background-color: white;">
                                    <div class="form-check form-switch">
                                       <input class="form-check-input" id="fromEmailCheck" name="fromEmailCheck" type="checkbox" role="switch" value="true">
                                       <label class="form-check-label" for="fromEmailCheck">E.A.L</label>
                                    </div>
                                 </div>
                                 <div class="col-8 p-0">
                                    <input type="text" name="fromEmail" id="fromEmail" class="form-control border-start-0 rounded-start-0" style="height: 43px;" value="[anl_20]@email.com">
                                 </div>
                              </div>
                           </div>
                           <!-- Reply-To -->
                           <div class="col-4">
                              <label for="replyTo" class="form-label fw-semibold">Reply-To</label>
                              <div class="row mx-0">
                                 <div class="col-4 p-0 border rounded-2 rounded-end-0 d-flex align-items-center justify-content-center" style="background-color: white;">
                                    <div class="form-check form-switch">
                                       <input class="form-check-input" id="replyToCheck" name="replyToCheck" type="checkbox" role="switch" value="true">
                                       <label class="form-check-label" for="replyToCheck">R.A.L</label>
                                    </div>
                                 </div>
                                 <div class="col-8 p-0">
                                    <input type="text" name="replyTo" id="replyTo" class="form-control border-start-0 rounded-start-0" style="height: 43px;" value="[anl_20]@email.com">
                                 </div>
                              </div>
                           </div>
                           <!-- Return Path -->
                           <div class="col-4">
                              <label for="returnPath" class="form-label fw-semibold">Return Path</label>
                              <div class="row mx-0">
                                 <div class="col-4 p-0 border rounded-2 rounded-end-0 d-flex align-items-center justify-content-center" style="background-color: white;">
                                    <div class="form-check form-switch">
                                       <input class="form-check-input" id="returnPathCheck" name="returnPathCheck" type="checkbox" role="switch" value="true">
                                       <label class="form-check-label" for="returnPathCheck">C.R</label>
                                    </div>
                                 </div>
                                 <div class="col-8 p-0">
                                    <input type="text" name="returnPath" id="returnPath" class="form-control border-start-0 rounded-start-0" style="height: 43px;" value="[anl_20]@email.com">
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- End Properties -->

                  <hr class="my-4">

                  <!-- Start Body -->
                  <div id="Body" class="card">
                     <div class="card-header d-flex justify-content-between align-items-center fw-semibold">
                        <h5 class="m-0 fw-semibold">
                           Body Section
                        </h5>
                        <div class="form-check form-switch d-flex gap-2 align-items-center">
                           <input class="form-check-input" type="checkbox" role="switch" id="tracking" checked>
                           <label class="form-check-label" for="tracking">Tracking</label>
                        </div>
                     </div>
                     <div class="card-body border-3 border-top border-success" style="background-color: #E2E2E2;">
                        <!-- Link & Attachements -->
                        <div class="row pb-3">
                           <!-- Link -->
                           <div class="col-6">
                              <label for="link" class="form-label fw-semibold">Link</label>
                              <input type="text" class="form-control mx-0" name="link" id="link" style="height: 43px;" placeholder="http://example.com">
                           </div>
                           <!-- Attachements -->
                           <div class="col-6">
                              <label for="attachements" class="form-label fw-semibold">Attachements</label>
                              <div class="row mx-0">
                                 <div class="col-8 p-0">
                                    <!-- Attachements name & Clear files button -->
                                    <div class="row mx-0" style="position: relative;">
                                       <input type="text" name="attachementsName" id="attachementsName" class="form-control rounded-end-0 col" style="height: 43px; font-size: 0.85rem; position: absolute;" placeholder="Your files names" disabled>
                                       <!-- Clear files button -->
                                       <button type="button" id="clearAttachements" class="btn btn-outline-dark rounded-0 p-auto invisible" style="z-index: 1; height: 43px; width: 43px; position: absolute; right: 0; border-color: transparent; padding-top: 2px;" onclick="clearFiles()">
                                          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
                                             <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"></path>
                                          </svg>
                                       </button>
                                    </div>
                                 </div>
                                 <input type="file" name="attachements[]" id="attachements" hidden multiple onchange="fileUpload()" oninput="fileUpload()">
                                 <label class="send btn btn-success col-4 rounded-start-0 d-flex justify-content-center align-items-center" for="attachements" style="height: 43px;">Upload</label>
                              </div>

                           </div>
                        </div>
                        <!-- Creative -->
                        <div id="creativeContainer" class="col">
                           <div class="row">
                              <!-- Labels -->
                              <div class="row mx-0">
                                 <!-- Creative Label -->
                                 <div class="col-6 p-0">
                                    <label for="creative" class="form-label fw-semibold">Creative</label>
                                 </div>
                                 <!-- Preview Label -->
                                 <!-- <div class="col-6">
                                    <label for="preview" class="form-label fw-semibold">Preview</label>
                                 </div> -->
                              </div>
                           </div>
                           <!-- TextAreas -->
                           <div class="textareas row mx-0 px-0 " style="position: relative;">
                              <div class="p-0 d-flex">
                                 <!-- Creative TextArea -->
                                 <div class="w-100 bg-white rounded">
                                    <div spellcheck="false" name="creative" class="creative form-control w-100 mx-0 px-0" style="height: 391px;" oninput="previewCreative(this)" required></div>
                                 </div>
                                 <!-- Vertical Separator -->
                                 <div class="bg-secondary-subtle border border-secondary rounded d-flex align-items-center" style="width: 1.3rem; cursor: pointer;" title="Collapse Subcreatives" onclick="collapseSubcreatives(this)">
                                    <!-- Right -->
                                    <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" class="d-none">
                                       <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z" />
                                    </svg>
                                    <!-- Left -->
                                    <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20">
                                       <path d="M560-240 320-480l240-240 56 56-184 184 184 184-56 56Z" />
                                    </svg>
                                 </div>
                                 <!-- Subcreatives -->
                                 <div class="subcreativesContainer w-100 ms-2 d-none" data-collapsed="true">
                                    <div class="subCreatives h-100 d-flex flex-column gap-2">
                                       <div class="h-100 d-flex flex-column align-items-stretch">
                                          <div class="d-flex justify-content-between align-items-center">
                                             <label for="" class="form-label fw-semibold m-0">Subcreative 1</label>
                                             <div class="fileContainer">
                                                <input type="file" name="subcreative1Attachement" id="subcreative1Attachement" hidden oninput="showSubcreativeAttachementName(event)">
                                                <!-- Subcreative 1 Attachement Label -->
                                                <label class="subcreativeAttachementLabel btn btn-secondary" for="subcreative1Attachement">Choose file</label>
                                                <!-- Subcreative 1 Attachement Clear Button -->
                                                <button class="btn btn-danger px-2" type="button" onclick="clearSubcreativeAttachement(this)">
                                                   <svg xmlns="http://www.w3.org/2000/svg" height="21" width="21" viewBox="0 -960 960 960" fill="currentColor">
                                                      <path d="m376-300 104-104 104 104 56-56-104-104 104-104-56-56-104 104-104-104-56 56 104 104-104 104 56 56Zm-96 180q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Z"></path>
                                                   </svg>
                                                </button>
                                             </div>
                                          </div>
                                          <textarea class="subcreative h-100 bg-white form-control" style="resize: none;"></textarea>
                                       </div>
                                       <div class="h-100 d-flex flex-column align-items-stretch">
                                          <div class="d-flex justify-content-between align-items-center">
                                             <label for="" class="form-label fw-semibold m-0">Subcreative 2</label>
                                             <div class="fileContainer">
                                                <input type="file" name="subcreative2Attachement" id="subcreative2Attachement" hidden oninput="showSubcreativeAttachementName(event)">
                                                <!-- Subcreative 2 Attachement Label -->
                                                <label class="subcreativeAttachementLabel btn btn-secondary" for="subcreative2Attachement">Choose file</label>
                                                <!-- Subcreative 2 Attachement Clear Button -->
                                                <button class="btn btn-danger px-2" type="button" onclick="clearSubcreativeAttachement(this)">
                                                   <svg xmlns="http://www.w3.org/2000/svg" height="21" width="21" viewBox="0 -960 960 960" fill="currentColor">
                                                      <path d="m376-300 104-104 104 104 56-56-104-104 104-104-56-56-104 104-104-104-56 56 104 104-104 104 56 56Zm-96 180q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Z"></path>
                                                   </svg>
                                                </button>
                                             </div>
                                          </div>
                                          <textarea class="subcreative h-100 bg-white form-control" style="resize: none;"></textarea>
                                       </div>
                                       <div class="h-100 d-flex flex-column align-items-stretch">
                                          <div class="d-flex justify-content-between align-items-center">
                                             <label for="" class="form-label fw-semibold m-0">Subcreative 3</label>
                                             <div class="fileContainer">
                                                <input type="file" name="subcreative3Attachement" id="subcreative3Attachement" hidden oninput="showSubcreativeAttachementName(event)">
                                                <!-- Subcreative 3 Attachement Label -->
                                                <label class="subcreativeAttachementLabel btn btn-secondary" for="subcreative3Attachement">Choose file</label>
                                                <!-- Subcreative 3 Attachement Clear Button -->
                                                <button class="btn btn-danger px-2" type="button" onclick="clearSubcreativeAttachement(this)">
                                                   <svg xmlns="http://www.w3.org/2000/svg" height="21" width="21" viewBox="0 -960 960 960" fill="currentColor">
                                                      <path d="m376-300 104-104 104 104 56-56-104-104 104-104-56-56-104 104-104-104-56 56 104 104-104 104 56 56Zm-96 180q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Z"></path>
                                                   </svg>
                                                </button>
                                             </div>
                                          </div>
                                          <textarea class="subcreative h-100 bg-white form-control" style="resize: none;"></textarea>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <!-- Remove Creative Button -->
                              <div class="removeCreative invisible">
                                 <button class="btn btn-danger w-100 h-100" type="button" style="padding-top: 5px;padding-bottom: 10px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
                                       <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"></path>
                                    </svg>
                                 </button>
                              </div>
                           </div>
                        </div>
                        <div class="pt-2 pb-3">
                           <button type="button" class="btn btn-play w-100 mx-0" onclick="addCreative()">Add Creative</button>
                           <!-- <button type="button" class="btn btn-secondary w-100 mx-0" onclick="getCreatives()">Get creatives</button> -->
                        </div>

                        <!-- Test After & Email Test -->
                        <div class="row pb-3">
                           <!-- Test after -->
                           <div class="col-6">
                              <label for="testAfter" class="form-label fw-semibold">
                                 Test after
                              </label>
                              <div class="row mx-0">
                                 <div class="p-0">
                                    <input type="number" class="form-control" id="testAfter" name="testAfter" placeholder="Test after" min="0" value="0" style="height: 43px;">
                                 </div>
                              </div>
                           </div>
                           <!-- Email Test -->
                           <div class="col-6">
                              <label for="emailTest" class="form-label fw-semibold">
                                 Email Test
                              </label>
                              <div class="row mx-0">
                                 <div class="p-0">
                                    <input type="text" class="form-control" id="emailTest" name="emailTest" placeholder="test@example.com; another-test@example.com" style="height: 43px;">
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- Start & Count -->
                        <div class="row pb-3">
                           <!-- Start -->
                           <div class="col-6">
                              <label for="start" class="form-label fw-semibold">
                                 Start
                              </label>
                              <div class="row mx-0">
                                 <div class="p-0">
                                    <input type="number" class="form-control" id="start" name="start" placeholder="Index of recipient to start with" min="0" value="0" style="height: 43px;">
                                 </div>
                              </div>
                           </div>
                           <!-- Count -->
                           <div class="col-6">
                              <label for="count" class="form-label fw-semibold">
                                 Count
                              </label>
                              <div class="row mx-0">
                                 <div class="p-0">
                                    <input type="number" class="form-control" id="count" name="count" placeholder="Number of recipients" min="1" style="height: 43px;">
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="d-flex justify-content-end">
                           <button id="test" type="button" class="send btn btn-success fw-semibold" style="width: 20%; height: 43px;" onclick="testNow()" disabled>Test</button>
                        </div>
                     </div>
                  </div>
                  <!-- End Body -->

                  <hr class="my-4">

                  <!-- Start Recipients -->
                  <div id="Recipients" class="card">
                     <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold m-0">
                           Recipients Section
                        </h5>
                        <div>
                           <select name="writeType" id="writeType" class="form-select" style="width: 180px;" onchange="changeRcptsWriteType(this)" onload="this.selectedIndex = 0">
                              <option value="manual" selected>Manual</option>
                              <option value="drag">Drag & Drop</option>
                              <option value="db">Choose from DB</option>
                           </select>
                        </div>
                     </div>
                     <div class="card-body border-3 border-top border-success" style="background-color: #E2E2E2;">
                        <!-- Recipients & Blacklist & Failed -->
                        <div class="row">
                           <!-- Recipients -->
                           <div class="col-4">
                              <div class="mb-2 d-flex justify-content-between align-items-center">
                                 <label for="recipients" class="form-label fw-semibold m-0">
                                    Recipients
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <span class="fst-italic">(<span id="nbrRecipients">0</span> recipients)</span>
                                 </label>
                                 <!-- Refresh from DB -->
                                 <button id="dbRefreshBtn" type="button" class="btn border-0 p-0 me-2 invisible" onclick="refreshFromDB(event)">
                                    <!-- Refresh Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" id="dbIcon" height="24" viewBox="0 -960 960 960" width="24">
                                       <path d="M480-160q-134 0-227-93t-93-227q0-134 93-227t227-93q69 0 132 28.5T720-690v-110h80v280H520v-80h168q-32-56-87.5-88T480-720q-100 0-170 70t-70 170q0 100 70 170t170 70q77 0 139-44t87-116h84q-28 106-114 173t-196 67Z" />
                                    </svg>
                                    <!-- Loader Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" id="dbLoader" class="d-none" width="24" height="24" viewBox="0 0 100 100" style="cursor: not-allowed;">
                                       <circle cx="50" cy="50" fill="none" stroke="black" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138">
                                          <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1" />
                                       </circle>
                                    </svg>
                                 </button>
                              </div>
                              <!-- Recipients textarea -->
                              <textarea spellcheck="false" name="recipients" id="recipients" class="form-control w-100" style="height: 400px; resize: none;" placeholder="Write recipients here..."></textarea>
                              <!-- Drop Zone -->
                              <div id="dropZone" class="form-control text-center text-secondary user-select-none w-100 border-secondary-subtle d-none flex-column justify-content-evenly" style="height: 400px; border-style: dashed;" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                                 <div class="childElements">Drag and drop .txt or .csv file here</div>
                                 <div class="childElements">Or</div>
                                 <label id="uploadButton" for="data" class="childElements btn btn-outline-play mx-auto">Browse files</label>
                                 <input id="data" name="data" type="file" accept="text/plain, text/csv" class="d-none" onchange="handleFileInput(event)" />
                              </div>
                              <!-- From DB -->
                              <!-- <div id="db" class="form-control d-none" style="height: 400px;"> -->

                              <!-- <select id="db" class="form-select d-none p-2" size="15" aria-label="Size 3 select example" style="height: 400px; overflow: auto;" ondblclick="">
                                 <option class="selectPlaceholder" disabled>Choose a country first and press refresh...</option>
                                 <option class="dataEntry">Choose a country first and press refresh...</option>
                              </select> -->

                              <div id="dbContainer" class="d-none position-relative" style="height: 400px; ">
                                 <!-- Data from DB will be displayed here... -->
                                 <div id="db" class="h-100 form-control d-flex flex-column p-2" style="overflow: auto;" ondblclick="console.log('hi')">
                                    <!-- Placeholder -->
                                    <div id="selectPlaceholder" class="text-secondary user-select-none">Choose a country first and press refresh...</div>
                                    <!-- Data Entry Example -->
                                    <div id="dataEntryExample" class="dataEntry d-none align-items-center" data-selected="false">
                                       <div class="dataInfos flex-fill">
                                          <div class="dataName fw-semibold">Data name</div>
                                          <div class="dataNbrRecipients" style="font-size: 0.8rem;">Data rcpts number</div>
                                       </div>
                                       <div class="loadRecipientBtnContainer">
                                          <button type="button" class="loadRecipientBtn btn border-0 " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Load data to recipents field">
                                             <svg xmlns="http://www.w3.org/2000/svg" height="30" width="30" viewBox="0 -960 960 960" fill="currentColor">
                                                <path d="M560-80v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T903-300L683-80H560Zm300-263-37-37 37 37ZM620-140h38l121-122-18-19-19-18-122 121v38ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v120h-80v-80H520v-200H240v640h240v80H240Zm280-400Zm241 199-19-18 37 37-18-19Z" />
                                             </svg>
                                          </button>
                                       </div>
                                    </div>
                                 </div>

                                 <!-- Loader -->
                                 <div id="recipientsLoader" class="d-flex align-items-center justify-content-center gap-3 h-100 w-100 bg-dark rounded invisible" style="position: absolute; top: 0; --bs-bg-opacity: 0.3">
                                    <div class="spinner-border " role="status">
                                       <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div class="fs-5 ">Please wait...</div>
                                 </div>
                              </div>
                           </div>
                           <!-- Blacklist -->
                           <div class="col-4">
                              <label for="blacklist" class="form-label fw-semibold" style="user-select: none; cursor: pointer;">
                                 Blacklist
                                 &nbsp;&nbsp;&nbsp;&nbsp;
                                 <span class="fst-italic text-danger" onclick="showBlacklistDialogue()">(<span id="nbrBlacklist">0</span> blacklisted)</span>
                              </label>
                              <!-- <textarea spellcheck="false" name="blacklist" id="blacklist" rows="15" class="form-control w-100" style="resize: none;"></textarea> -->
                              <div style="position: relative;">
                                 <div spellcheck="false" name="blacklist" id="blacklist" class="form-control w-100" style="height: 400px; overflow: auto;" contenteditable="true" onpaste="organizeBlacklist(event)"></div>
                                 <div id="blacklistSpinner" class="d-flex align-items-center justify-content-center gap-3 h-100 w-100 bg-dark rounded invisible" style="position: absolute; top: 0; --bs-bg-opacity: 0.3">
                                    <div class="spinner-border " role="status">
                                       <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div class="fs-5 ">Please wait...</div>
                                 </div>
                              </div>

                           </div>
                           <!-- Failed -->
                           <div class="col-4">
                              <label for="failed" class="form-label fw-semibold">Failed</label>
                              <div spellcheck="false" name="failed" id="failed" rows="15" class="form-control w-100 bg-white" style="height: 400px; overflow: auto;"></div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- End Recipients -->

                  <hr class="my-4">

                  <!-- Start Result -->
                  <div id="Result" class="card">
                     <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-semibold">
                           Result Section
                        </h5>
                        <span class="text-danger" id="remainingLabel"></span>
                     </div>
                     <div id="responseTable" class="card-body border-3 border-top border-success" style="height: 377px; overflow: auto; background-color: white;">
                        <table class="table table-striped">
                           <tbody id="responseArea">

                           </tbody>
                        </table>
                     </div>
                  </div>
                  <!-- End Result -->



               </div>
               <!-- History -->
               <div class="tab-pane h-100" id="nav-history" class="pt-2" role="tabpanel" aria-labelledby="nav-history-tab" tabindex="0" style="height: initial;">
                  <iframe id="historyIframe" width="100%" height="100%" src="https://45.145.6.18/database/history/history.html" title="History" frameborder="0" allowfullscreen></iframe>
               </div>
               <!-- Data -->
               <div class="tab-pane h-100" id="nav-data" class="pt-2" role="tabpanel" aria-labelledby="nav-data-tab" tabindex="0" style="height: initial;">
                  <iframe id="dataIframe" width="100%" height="100%" src="https://45.145.6.18/database/data/data.html" title="Data" frameborder="0" allowfullscreen style="height: 110%;"></iframe>
               </div>
               <!-- Imap Checker -->
               <div class="tab-pane h-100" id="nav-imap" class="pt-2" role="tabpanel" aria-labelledby="nav-imap-tab" tabindex="0" style="height: initial;">
                  <iframe id="imapIframe" width="100%" height="100%" src="./public/imap/index.html" title="Imap Checker" frameborder="0" allowfullscreen style="height: 107%;"></iframe>
               </div>
               <!-- Tracking -->
               <div class="tab-pane h-100" id="nav-tracking" class="pt-2" role="tabpanel" aria-labelledby="nav-tracking-tab" tabindex="0" style="height: initial;">
                  <iframe id="trackingIframe" width="100%" height="100%" src="https://45.145.6.18/database/tracking" title="Tracking" frameborder="0" allowfullscreen style="height: 107%;"></iframe>
               </div>
               <!-- SMTP Check -->
               <div class="tab-pane h-100" id="nav-smtp-check" class="pt-2" role="tabpanel" aria-labelledby="nav-smtp-check-tab" tabindex="0" style="height: initial;">
                  <iframe id="smtpCheckIframe" width="100%" height="100%" src="./public/combo_check/" title="SMTP Check" frameborder="0" allowfullscreen style="height: 107%;"></iframe>
               </div>
            </div>
            <!-- Blacklist Dialogue -->
            <div id="blacklistDialogue" class="h-100 w-100 bg-dark d-flex invisible" style="position: fixed; top: 0; left: 0; --bs-bg-opacity: .5;">
               <div class="bg-white m-auto border border-black rounded p-4" style="height: 55%; width: 40%; position: relative;">
                  <div class="d-flex justify-content-end w-100" style="position: absolute; top: 0; left: 0;">
                     <button type="button" id="clearAttachements" class="btn btn-outline-dark p-auto m-2" style="z-index: 1; height: 43px; width: 43px; border-color: transparent; padding-top: 2px;" onclick="closeBlacklistDialogue()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
                           <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"></path>
                        </svg>
                     </button>
                  </div>
                  <div class="fw-semibold"><u>List of blacklisted recipients:</u></div>
                  <textarea id="blacklistList" class="form-control border border-danger rounded mt-4 text-black" style="height: 90%; width: 100%; resize: none; cursor: text;" disabled></textarea>
               </div>
            </div>

            <!-- Edit Document Dialogue -->
            <div id="documentDataDialogue" class="h-100 w-100 bg-dark d-flex invisible" style="position: fixed; top: 0; left: 0; --bs-bg-opacity: .5;">
               <div class="bg-white m-auto border border-black rounded p-4" style="height: 80%; width: 60%; position: relative;">
                  <div class="d-flex justify-content-end w-100" style="position: absolute; top: 0; left: 0;">
                     <button type="button" id="closeDocumentDataDialogue" class="btn btn-outline-dark p-auto m-2" style="z-index: 1; height: 43px; width: 43px; border-color: transparent; padding-top: 2px;" onclick="closeDataDialogue();">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
                           <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"></path>
                        </svg>
                     </button>
                  </div>
                  <div class="fw-semibold"><u>Document data:</u></div>
                  <textarea id="documentData" spellcheck="false" class="form-control rounded mt-4 text-black" style="height: 85%; width: 100%; resize: none; cursor: text; font-family: monospace; font-size: 0.8rem;"></textarea>
                  <div class="my-4 d-flex justify-content-end fw-semibold">
                     <button class="btn btn-success" type="button" style="width: 150px;" onclick="saveDocumentData()">Save</button>
                  </div>
               </div>
            </div>

            <div class="position-fixed bottom-0 end-0 py-4 px-3 d-flex flex-column align-items-center gap-4 invisible">
               <div class="d-flex flex-column align-items-center gap-2 rounded invisible visible" style="padding: 15px; width: max-content; background-color: rgba(0, 0, 0, 0.5);">
                  <button type="button" class="btn btn-dark fw-semibold" title="SMTP Check" style="border-radius: 50rem; width: 50px;" onclick="navSmtpCheckTab.click()">S</button>
                  <button type="button" class="btn btn-purple fw-semibold" title="Tracking" style="border-radius: 50rem; width: 50px;" onclick="navTrackingTab.click()">T</button>
                  <button type="button" class="btn btn-danger fw-semibold" title="IMAP Checker" style="border-radius: 50rem; width: 50px;" onclick="navImapTab.click()">I</button>
                  <button type="button" class="btn btn-success fw-semibold" title="Data" style="border-radius: 50rem; width: 50px;" onclick="navDataTab.click()">D</button>
                  <button type="button" class="btn btn-warning fw-semibold" title="History" style="border-radius: 50rem; width: 50px;" onclick="navHistoryTab.click()">H</button>
                  <button type="button" class="btn btn-play fw-semibold" title="Send" style="border-radius: 50rem; width: 50px;" onclick="navSendTab.click()">S</button>
               </div>
               <!-- 3 points Button -->
               <button class="btn btn-play rounded-circle visible" type="button" style="width: 50px; height: 50px; box-shadow: 0px 0px 30px 4px #8B8B8B;" title="More" onclick="this.previousElementSibling.classList.toggle('invisible')">
                  <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor">
                     <path d="M480-160q-33 0-56.5-23.5T400-240q0-33 23.5-56.5T480-320q33 0 56.5 23.5T560-240q0 33-23.5 56.5T480-160Zm0-240q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm0-240q-33 0-56.5-23.5T400-720q0-33 23.5-56.5T480-800q33 0 56.5 23.5T560-720q0 33-23.5 56.5T480-640Z" />
                  </svg>
               </button>
               <!-- Edit Document Button -->
               <button class="btn btn-play rounded-circle visible" type="button" style="width: 50px; height: 50px; box-shadow: 0px 0px 30px 4px #8B8B8B;" title="Edit Document" onclick="editDocumentData()">
                  <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor">
                     <path d="M560-80v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T903-300L683-80H560Zm300-263-37-37 37 37ZM620-140h38l121-122-18-19-19-18-122 121v38ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v120h-80v-80H520v-200H240v640h240v80H240Zm280-400Zm241 199-19-18 37 37-18-19Z" />
                  </svg>
               </button>
               <!-- Top Button -->
               <button class="btn btn-play rounded-circle visible" type="button" style="width: 50px; height: 50px; box-shadow: 0px 0px 30px 4px #8B8B8B;" title="Scroll to top" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
                  <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor">
                     <path d="M440-160v-487L216-423l-56-57 320-320 320 320-56 57-224-224v487h-80Z" />
                  </svg>
               </button>
            </div>
         </div>
      </div>
      <!-- End Main Menu -->

   </form>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="./public/js/bootstrap.bundle.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
   <script src="./public/js/script.js"></script>
   <script>
      $('.creative').summernote({
         placeholder: 'Creative...',
         tabsize: 2,
         height: 391,
         minheight: 391,
         spellCheck: false,
         fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 'Tahoma', 'Times New Roman', 'Trebuchet MS', 'Verdana'],
         toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']],
         ],
         addDefaultFonts: true
      });
   </script>
</body>

</html>