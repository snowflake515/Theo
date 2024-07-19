<?php

  $openai_base_url = 'https://api.openai.com/v1/chat/completions';
$data = [
    'model' => "gpt-4o",
    'messages' => [
        [
            "role" => "system",
            "content" => 'you are doctor who take care patient in hospital'
        ],
        [
            "role" => "user",
            "content" => 
              "
              Please remove history first.

              $InputStr
              Please create Clinical report based on above data.

              Note:  these are the headers of our templates for Annual Wellness Visits or Health Risk Assessments.  Every header should have TWO sub-headers:
              •	Preliminary Assessment
              •	Proposed Intervention 
              Some of the headers will be NULL because data has not been collected.  Otherwise, this is the complete list of headers that should help “train” ChatGPT.
              Our Annual Wellness Visits are “screenings” when data is taken in by the patient.  Only upon review and approval of a licensed provider are “assessments” rendered.  So we must label our headers as “Preliminary Assessments” and “Proposed Interventions”, etc. etc.

              The structure and modalities of report should be like this:
              Large sections with a number, font-size is <h4> and the next sub-headers starts with -, font-weight is bold 
              and <br> is required once between the content of sub-header('- Preliminary Assessment:') and other sub-header('- Proposed Intervention:').
              Sub-header's content starts with &middot; in the html.

              And convert to html code(keep structure and modalities of your response) so I can display your response on the html.
              rid of ```html or ```.
              <br> is required once between the content of sub-header('- Preliminary Assessment:') and other sub-header('- Proposed Intervention:').
              ",
        ],
    ],
    'temperature' =>  1,
    'max_tokens' => 4096,
    'frequency_penalty' => 0,
    'presence_penalty' => 0,
];

$data1 = [
  'model' => "gpt-4o",
  'messages' => [
      [
          "role" => "system",
          "content" => 'you are doctor who take care patient in hospital'
      ],
      [
          "role" => "user",
          "content" => 
            "",
      ],
  ],
  'temperature' =>  1,
  'max_tokens' => 4096,
  'frequency_penalty' => 0,
  'presence_penalty' => 0,
];

$apiKey = 'sk-proj-9vXixHi1WeYmwNHld3yuT3BlbkFJckwEQq4FyVfcRq4rl9Z9'; // Secure this key appropriately
$headers = [
    'Content-Type: application/json',
    'Authorization: ' . 'Bearer ' . $apiKey
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $openai_base_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$error = curl_errno($ch);
$data = json_decode($response, true);
  if ($error) {
    log_message('error', "AI CARE PLAN Fail");
    log_message('error', curl_error($ch));
  } else {
  }
  curl_close($ch);

  $data['HeaderKey'] = $HeaderKey;
  $data['PatientKey'] = $PatientKey;
  $data['HeaderMasterKey'] = $HeaderMasterKey;
  $data['FreeTextKey'] = $FreeTextKey;
  $data['SOHeaders'] = $SOHeaders;
  
  $data['data_db'] = $data_db;
  $BodyFontInfo = getBodyFontInfo($data, $HeaderKey);
  $DefaultStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: 14  " .  "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  $ColumnHeaderStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: 14 " .  "px; font-weight: bold; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  ?>
  <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
    <tr>
      <td width="7">&nbsp;</td>
      <td>
        <p style="<?php echo $DefaultStyle ?>">
          <?php echo $data['choices'][0]['message']['content'] ?>
        </p>
      </td>
    </tr>
  </table>

