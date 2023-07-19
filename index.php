<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" href="./public/assets/favicon.ico" type="image/x-icon">
   <link rel="stylesheet" href="./public/stylesheets/bootstrap.css">
   <link rel="stylesheet" href="./public/stylesheets/styles.css">

   <title>St-Com</title>
</head>

<body class="row w-100">
   <form action="public/functions/send_email.php" method="post" class="p-0" id="sendForm">
      <!-- Start Side panel -->
      <div class="col p-0 position-fixed start-0 h-100" style="max-width: 220px; background-color: #8fa1a3;">
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
            <a href="#Status" class="btn py-3 px-0 fw-semibold text-white rounded-0 navigation">
               <div class="d-flex flex-row justify-content-between px-4">
                  <div>Status</div>
                  <img src="./public/assets/status.svg" alt="">
               </div>
            </a>
            <button type="submit" class="send btn btn-success bord-0 mt-4 mb-3 mx-auto text-white fw-semibold" style="height: 43px; width: 85%;" onclick="checkFields()">Start</button>
            <div class="d-flex justify-content-evenly mx-auto h-auto p-0 invisible" style="width: 85%;">
               <button class="col-3 btn btn-primary border-0" style="background-color: #3598DC;">
                  <img src="./public/assets/play.svg" alt="Play" class=" pb-1">
               </button>
               <button class="col-3 btn btn-warning border-0">
                  <img src="./public/assets/pause.svg" alt="Pause" class=" pb-1">
               </button>
               <button class="col-3 btn btn-danger border-0">
                  <img src="./public/assets/stop.svg" alt="Cancel" class=" pb-1">
               </button>
            </div>
         </div>
      </div>
      <!-- End Side Panel -->
      <!-- Start Main Menu -->
      <div class="d-flex">
         <div style="width: 250px"></div>
         <div class="col mb-4 container-xxl" style="padding-top: 10px;">
            <div>
               <h4>St-Com Mailing</h4>
            </div>
            <hr>
            <!-- Start Servers -->
            <div id="Servers" class="card">
               <h5 class="card-header fw-semibold">
                  Servers Section
               </h5>
               <div class="card-body border-3 border-top border-success">
                  <div class="row">
                     <!-- Start Server Field -->
                     <div class="col">
                        <label for="servers" class="form-label fw-semibold">
                           Servers
                        </label>
                        <textarea name="servers" id="servers" class="form-control" style="height: 134px; resize: none;" placeholder="Format 1: Host:Port:TLS:User:Pass&#10;&#10;Format 2: Host:Port:SSL:User:Pass" oninput="changeTimeValues()" required></textarea>
                     </div>
                     <!-- End Server Field -->
                     <div class="col">
                        <div class="row pb-3">
                           <div class="col">
                              <label for="pauseAfterSend" class="form-label fw-semibold">
                                 Pause After Send <span class="fst-italic">(Seconds)</span>
                              </label>
                              <input type="number" name="pauseAfterSend" id="pauseAfterSend" class="form-control" style="height: 43px;" value="5" min="1" onchange="changeTimeValues()">
                           </div>
                           <div class=" col">
                              <label for="rotationAfter" class="form-label fw-semibold">
                                 Rotation After <span class="fst-italic">(Minutes)</span>
                              </label>
                              <input type="number" name="rotationAfter" id="rotationAfter" class="form-control" style="height: 43px;" value="1" min="1">
                           </div>
                        </div>
                        <div class=" row">
                           <!-- <div class="col">
                           <label for="emailPerSecond" class="form-label fw-semibold">
                              Email per second
                           </label>
                           <input type="number" name="emailPerSecond" id="emailPerSecond" class="form-control" style="height: 43px;" value="1" min="1" onchange="changeTimeValues()">
                        </div> -->
                           <div class="col">
                              <label for="BCCnumber" class="form-label fw-semibold">
                                 Number of Emails In Bcc
                              </label>
                              <input type="number" name="BCCnumber" id="BCCnumber" class="form-control" style="height: 43px;" value="3" min="1" max="10" onchange="changeTimeValues()">
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
               <div class="card-body border-3 border-top border-success">
                  <div class="row">
                     <!-- Custom Header -->
                     <div class="col">
                        <label for="headers" class="form-label fw-semibold">
                           Custom Header
                        </label>
                        <textarea name="headers" id="headers" class="form-control" style="height: 134px; resize: none; font-size: 0.85rem;">Message-ID: <[anl_20]@[domain]>&#10;X-Mailer: St-Com v1.0-Ref[n_2]&#10;Auto-Submitted: auto-generated&#10;X-Auto-Response-Suppress: OOF, AutoReply&#10;X-Abuse: Please report abuse here <mailto:abuse@[domain]?c=[n_10]></textarea>
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
               <div class="card-body border-3 border-top border-success">
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
                                    <div class="row flex-nowrap align-items-center justify-content-around border rounded-2 rounded-end-0 w-auto p-0" style="height: 43px;">
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
               <div class="card-body border-3 border-top border-success">
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
                              <input type="text" class="form-control rounded-start-0" id="fromName" name="fromName" placeholder="From Name" value="[au_10]" style="height: 43px;">
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
                              <input type="text" class="form-control rounded-start-0" id="subject" name="subject" placeholder="From Name" value="[au_10]" style="height: 43px;">
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
                           <div class="col-4 p-0 border rounded-2 rounded-end-0 d-flex align-items-center justify-content-center">
                              <div class="form-check form-switch">
                                 <input class="form-check-input" id="fromEmailCheck" name="fromEmailCheck" type="checkbox" role="switch" value="true" checked>
                                 <label class="form-check-label" for="fromEmailCheck">E.A.L</label>
                              </div>
                           </div>
                           <div class="col-8 p-0">
                              <input type="text" name="fromEmail" id="fromEmail" class="form-control border-start-0 rounded-start-0" style="height: 43px;">
                           </div>
                        </div>
                     </div>
                     <!-- Reply-To -->
                     <div class="col-4">
                        <label for="replyTo" class="form-label fw-semibold">Reply-To</label>
                        <div class="row mx-0">
                           <div class="col-4 p-0 border rounded-2 rounded-end-0 d-flex align-items-center justify-content-center">
                              <div class="form-check form-switch">
                                 <input class="form-check-input" id="replyToCheck" name="replyToCheck" type="checkbox" role="switch" value="true">
                                 <label class="form-check-label" for="replyToCheck">R.A.L</label>
                              </div>
                           </div>
                           <div class="col-8 p-0">
                              <input type="text" name="replyTo" id="replyTo" class="form-control border-start-0 rounded-start-0" style="height: 43px;">
                           </div>
                        </div>
                     </div>
                     <!-- Return Path -->
                     <div class="col-4">
                        <label for="returnPath" class="form-label fw-semibold">Return Path</label>
                        <div class="row mx-0">
                           <div class="col-4 p-0 border rounded-2 rounded-end-0 d-flex align-items-center justify-content-center">
                              <div class="form-check form-switch">
                                 <input class="form-check-input" id="returnPathCheck" name="returnPathCheck" type="checkbox" role="switch" value="true">
                                 <label class="form-check-label" for="returnPathCheck">C.R</label>
                              </div>
                           </div>
                           <div class="col-8 p-0">
                              <input type="text" name="returnPath" id="returnPath" class="form-control border-start-0 rounded-start-0" style="height: 43px;">
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
               <h5 class="card-header fw-semibold">
                  Body Section
               </h5>
               <div class="card-body border-3 border-top border-success">
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
                              <input type="text" name="attachementsName" id="attachementsName" class="form-control rounded-end-0" style="height: 43px; font-size: 0.85rem;" placeholder="Your files names" disabled>
                           </div>
                           <input type="file" name="attachements" id="attachements" hidden multiple oninput="fileUpload()">
                           <label class="send btn btn-success col-4 rounded-start-0 d-flex justify-content-center align-items-center" for="attachements" style="height: 43px;">Upload</label>
                        </div>

                     </div>
                  </div>
                  <!-- Creative and Preview -->
                  <div class="row">
                     <!-- Creative -->
                     <div class="col-6">
                        <label for="creative" class="form-label fw-semibold">Creative</label>
                        <textarea name="creative" id="creative" rows="15" class="form-control w-100 language-html" style="font-family: monospace; resize: none;" oninput="previewCreative()" required></textarea>
                     </div>
                     <!-- Preview -->
                     <div class="col-6">
                        <label for="preview" class="form-label fw-semibold">Preview</label>
                        <div name="preview" id="preview" class="form-control w-100" style="height: 391px; overflow: auto;"></div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- End Body -->

            <hr class="my-4">

            <!-- Start Recipients -->
            <div id="Recipients" class="card">
               <h5 class="card-header fw-semibold">
                  Recipients Section
               </h5>
               <div class="card-body border-3 border-top border-success">
                  <!-- Recipients & Blacklist & Failures -->
                  <div class="row">
                     <!-- Recipients -->
                     <div class="col-4">
                        <label for="recipients" class="form-label fw-semibold">Recipients</label>
                        <textarea name="recipients" id="recipients" rows="15" class="form-control w-100" style="resize: none;" oninput="changeTimeValues()" required></textarea>
                     </div>
                     <!-- Blacklist -->
                     <div class=" col-4">
                        <label for="blacklist" class="form-label fw-semibold">Blacklist</label>
                        <textarea name="blacklist" id="blacklist" rows="15" class="form-control w-100" style="resize: none;"></textarea>
                     </div>
                     <!-- Failures -->
                     <div class="col-4">
                        <label for="failures" class="form-label fw-semibold">Failures</label>
                        <textarea name="failures" id="failures" rows="15" class="form-control w-100 bg-white" style="resize: none;" disabled></textarea>
                     </div>
                  </div>
               </div>
            </div>
            <!-- End Recipients -->

            <hr class="my-4">

            <!-- Start Status -->
            <div id="Status" class="card">
               <h5 class="card-header fw-semibold">
                  Status Section
               </h5>
               <div class="card-body border-3 border-top border-success" style="height: 377px;">
                  <table class="table table-striped">
                     <tbody id="responseArea">

                     </tbody>
                  </table>
               </div>
            </div>
            <!-- End Status -->

            <hr class="my-4">

            <!-- Start Result -->
            <div id="Result" class="card">
               <h5 class="card-header fw-semibold">
                  Result Section
               </h5>
               <div class="card-body border-3 border-top border-success" id="responseArea" style="height: 377px;">

               </div>
            </div>
            <!-- End Result -->
         </div>
      </div>
      <!-- End Main Menu -->
   </form>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="./public/js/script.js"></script>
</body>

</html>