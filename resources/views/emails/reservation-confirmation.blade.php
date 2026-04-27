<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',Arial,sans-serif; background:#f4f6f0; color:#1a3a2a; }
    .wrapper { max-width:600px; margin:30px auto; background:white; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08); }
    .header { background:linear-gradient(135deg,#1a3a2a,#2d5a3d); padding:40px 32px; text-align:center; }
    .header h1 { color:#c9a96e; font-size:26px; font-weight:300; }
    .header p { color:rgba(255,255,255,0.6); font-size:13px; margin-top:6px; }
    .check-circle { width:64px; height:64px; background:#c9a96e; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:28px; }
    .body { padding:32px; }
    .greeting { font-size:20px; font-weight:600; color:#1a3a2a; margin-bottom:8px; }
    .subtitle { font-size:14px; color:#6b7280; line-height:1.6; margin-bottom:28px; }
    .summary-card { background:linear-gradient(135deg,#1a3a2a,#2d5a3d); border-radius:16px; padding:28px; margin-bottom:28px; }
    .summary-card h3 { color:#c9a96e; font-size:12px; text-transform:uppercase; letter-spacing:2px; margin-bottom:20px; }
    .summary-row { display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.08); }
    .summary-row:last-child { border:none; padding-top:16px; }
    .summary-label { color:rgba(255,255,255,0.5); font-size:13px; }
    .summary-value { color:white; font-size:13px; font-weight:600; }
    .total-value { color:#c9a96e; font-size:20px; font-weight:700; }
    .amenities { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:28px; }
    .amenity { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:20px; padding:8px 16px; font-size:13px; color:#166534; font-weight:500; }
    .info-box { background:#f9fafb; border-radius:12px; padding:20px; margin-bottom:28px; }
    .info-box p { font-size:13px; color:#6b7280; line-height:1.7; }
    .footer { background:#f9fafb; padding:24px 32px; text-align:center; border-top:1px solid #f0f0f0; }
    .footer p { font-size:12px; color:#9ca3af; }
    .footer strong { color:#c9a96e; }
  </style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <div class="check-circle">✅</div>
    <h1>Réservation confirmée !</h1>
    <p>Merci pour votre confiance</p>
  </div>

  <div class="body">
    <p class="greeting">Bonjour {{ $reservation->user->name }},</p>
    <p class="subtitle">
      Votre réservation à la <strong>Ferme Khadija</strong> a bien été reçue.
      @if($reservation->payment_method === 'card')
        Votre paiement a été validé avec succès.
      @else
        Nous avons bien reçu votre preuve de virement. Votre réservation sera confirmée après vérification.
      @endif
    </p>

    <!-- Récapitulatif -->
    <div class="summary-card">
      <h3>Récapitulatif de votre séjour</h3>
      <div class="summary-row">
        <span class="summary-label">📅 Arrivée</span>
        <span class="summary-value">{{ $reservation->start_date->format('d/m/Y') }}</span>
      </div>
      <div class="summary-row">
        <span class="summary-label">📅 Départ</span>
        <span class="summary-value">{{ $reservation->end_date->format('d/m/Y') }}</span>
      </div>
      <div class="summary-row">
        <span class="summary-label">🌙 Durée</span>
        <span class="summary-value">{{ $reservation->total_days }} jour(s)</span>
      </div>
      <div class="summary-row">
        <span class="summary-label">💰 Avance payée</span>
        <span class="summary-value" style="color:#86efac">{{ number_format($reservation->advance_amount,0) }} DH</span>
      </div>
      <div class="summary-row">
        <span class="summary-label">💳 Total séjour</span>
        <span class="total-value">{{ number_format($reservation->total_price,0) }} DH</span>
      </div>
    </div>

    <!-- Équipements -->
    <p style="font-size:11px;font-weight:700;color:#c9a96e;text-transform:uppercase;letter-spacing:2px;margin-bottom:14px;">
      Ce qui vous attend
    </p>
    <div class="amenities">
      <span class="amenity">🏠 Maison complète</span>
      <span class="amenity">🏊 Piscine privée</span>
      <span class="amenity">🌿 Espace vert</span>
      <span class="amenity">🅿️ Parking gratuit</span>
    </div>

    <!-- Reste à payer -->
    <div class="info-box">
      <p>
        💡 <strong>Reste à payer sur place :</strong>
        {{ number_format($reservation->total_price - $reservation->advance_amount, 0) }} DH<br>
        Ce montant sera réglé à votre arrivée à la ferme.
      </p>
    </div>

    <!-- Contact -->
    <div class="info-box" style="background:#fff8ee; border:1px solid #fed7aa;">
      <p>
        📞 Pour toute question, contactez-nous :<br>
        <strong>Email :</strong> contact@fermekhadija.ma<br>
        <strong>Réservation #{{ $reservation->id }}</strong>
      </p>
    </div>
  </div>

  <div class="footer">
    <p>🌾 <strong>Ferme Khadija</strong> · Casablanca, Maroc</p>
    <p style="margin-top:6px">Cet email a été envoyé automatiquement suite à votre réservation.</p>
  </div>
</div>
</body>
</html>