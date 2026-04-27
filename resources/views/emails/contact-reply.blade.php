<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',Arial,sans-serif; background:#f4f6f0; color:#1a3a2a; }
    .wrapper { max-width:600px; margin:30px auto; background:white; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08); }
    .header { background:linear-gradient(135deg,#1a3a2a,#2d5a3d); padding:32px; text-align:center; }
    .header h1 { color:#c9a96e; font-size:22px; font-weight:300; }
    .header p { color:rgba(255,255,255,0.5); font-size:13px; margin-top:6px; }
    .body { padding:32px; }
    .greeting { font-size:18px; font-weight:600; margin-bottom:16px; }
    .original-msg { background:#f9fafb; border-left:3px solid #c9a96e; border-radius:0 12px 12px 0; padding:16px 20px; margin:20px 0; }
    .original-msg p { font-size:12px; color:#9ca3af; margin-bottom:8px; text-transform:uppercase; letter-spacing:1px; }
    .original-msg blockquote { font-size:13px; color:#6b7280; line-height:1.7; font-style:italic; }
    .reply-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:24px; margin:24px 0; }
    .reply-box p.label { font-size:11px; color:#059669; text-transform:uppercase; letter-spacing:1px; font-weight:700; margin-bottom:12px; }
    .reply-box p.content { font-size:14px; color:#1a3a2a; line-height:1.8; white-space:pre-wrap; }
    .footer { background:#f9fafb; padding:24px 32px; text-align:center; border-top:1px solid #f0f0f0; }
    .footer p { font-size:12px; color:#9ca3af; }
    .footer strong { color:#c9a96e; }
  </style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>🌾 Ferme Khadija</h1>
    <p>Réponse à votre message</p>
  </div>

  <div class="body">
    <p class="greeting">Bonjour {{ $contact->name }},</p>
    <p style="font-size:14px; color:#6b7280; line-height:1.7; margin-bottom:20px;">
      Merci de nous avoir contactés. Voici notre réponse à votre message :
    </p>

    <!-- Message original -->
    <div class="original-msg">
      <p>Votre message</p>
      <blockquote>{{ $contact->message }}</blockquote>
    </div>

    <!-- Réponse -->
    <div class="reply-box">
      <p class="label">Notre réponse</p>
      <p class="content">{{ $replyMessage }}</p>
    </div>

    <p style="font-size:13px; color:#9ca3af; margin-top:20px;">
      Si vous avez d'autres questions, n'hésitez pas à nous contacter de nouveau.
    </p>
  </div>

  <div class="footer">
    <p>🌾 <strong>Ferme Khadija</strong> · Casablanca, Maroc</p>
    <p style="margin-top:4px">contact@fermekhadija.ma</p>
  </div>
</div>
</body>
</html>