<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nouvelle Réservation</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f0; color: #1a3a2a; }
    .wrapper { max-width: 600px; margin: 30px auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
    .header { background: linear-gradient(135deg, #1a3a2a, #2d5a3d); padding: 40px 32px; text-align: center; }
    .header h1 { color: #c9a96e; font-size: 26px; font-weight: 300; letter-spacing: 1px; }
    .header p { color: rgba(255,255,255,0.6); font-size: 13px; margin-top: 6px; }
    .badge { display: inline-block; background: #c9a96e; color: #1a3a2a; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; margin-top: 12px; letter-spacing: 1px; text-transform: uppercase; }
    .body { padding: 32px; }
    .alert-box { background: #fff8ee; border: 1px solid #f0d9a8; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; }
    .alert-box span { font-size: 24px; }
    .alert-box p { font-size: 14px; color: #8b6f47; font-weight: 500; }
    .section-title { font-size: 11px; font-weight: 700; color: #c9a96e; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid #f0f0f0; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 28px; }
    .info-item { background: #f9fafb; border-radius: 10px; padding: 14px; }
    .info-label { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
    .info-value { font-size: 14px; font-weight: 600; color: #1a3a2a; }
    .price-box { background: linear-gradient(135deg, #1a3a2a, #2d5a3d); border-radius: 12px; padding: 24px; margin-bottom: 28px; text-align: center; }
    .price-box .label { color: rgba(255,255,255,0.6); font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
    .price-box .amount { color: #c9a96e; font-size: 36px; font-weight: 700; margin: 8px 0; }
    .price-box .sub { color: rgba(255,255,255,0.5); font-size: 12px; }
    .price-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
    .price-row:last-child { border: none; font-weight: 700; color: #1a3a2a; }
    .method-badge { display: inline-flex; align-items: center; gap: 6px; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; font-size: 12px; font-weight: 600; padding: 6px 14px; border-radius: 20px; }
    .footer { background: #f9fafb; padding: 24px 32px; text-align: center; border-top: 1px solid #f0f0f0; }
    .footer p { font-size: 12px; color: #9ca3af; line-height: 1.6; }
    .footer strong { color: #c9a96e; }
    .action-btn { display: inline-block; background: #c9a96e; color: #1a3a2a; font-weight: 700; font-size: 13px; padding: 12px 28px; border-radius: 25px; text-decoration: none; margin: 16px 0; }
  </style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>🌾 Ferme Khadija</h1>
    <p>Nouvelle réservation reçue</p>
    <span class="badge">À traiter</span>
  </div>

  <div class="body">
    <div class="alert-box">
      <span>📬</span>
      <p>Une nouvelle réservation vient d'être soumise et attend votre validation.</p>
    </div>

    <!-- Client -->
    <p class="section-title">Informations client</p>
    <div class="info-grid">
      <div class="info-item">
        <div class="info-label">Nom</div>
        <div class="info-value">{{ $reservation->user->name }}</div>
      </div>
      <div class="info-item">
        <div class="info-label">Email</div>
        <div class="info-value" style="font-size:12px">{{ $reservation->user->email }}</div>
      </div>
    </div>

    <!-- Séjour -->
    <p class="section-title">Détails du séjour</p>
    <div class="info-grid">
      <div class="info-item">
        <div class="info-label">Date d'arrivée</div>
        <div class="info-value">{{ $reservation->start_date->format('d/m/Y') }}</div>
      </div>
      <div class="info-item">
        <div class="info-label">Date de départ</div>
        <div class="info-value">{{ $reservation->end_date->format('d/m/Y') }}</div>
      </div>
      <div class="info-item">
        <div class="info-label">Durée</div>
        <div class="info-value">{{ $reservation->total_days }} jour(s)</div>
      </div>
      <div class="info-item">
        <div class="info-label">Réservation #</div>
        <div class="info-value">#{{ $reservation->id }}</div>
      </div>
    </div>

    <!-- Prix -->
    <div class="price-box">
      <div class="label">Montant total</div>
      <div class="amount">{{ number_format($reservation->total_price, 0, ',', ' ') }} DH</div>
      <div class="sub">{{ $reservation->total_days }} jours × 800 DH/jour</div>
    </div>

    <div style="margin-bottom: 28px;">
      <div class="price-row">
        <span style="color:#6b7280">Avance réglée</span>
        <span style="color:#059669; font-weight:600">{{ number_format($reservation->advance_amount, 0) }} DH</span>
      </div>
      <div class="price-row">
        <span style="color:#6b7280">Reste à payer</span>
        <span>{{ number_format($reservation->total_price - $reservation->advance_amount, 0) }} DH</span>
      </div>
      <div class="price-row">
        <span>Moyen de paiement</span>
        <span class="method-badge">
          {{ $reservation->payment_method === 'card' ? '💳 Carte Stripe' : '🏦 Virement bancaire' }}
        </span>
      </div>
    </div>

    @if($reservation->payment_method === 'bank_transfer')
    <div style="background:#fff8ee; border:1px solid #fed7aa; border-radius:12px; padding:16px; margin-bottom:24px;">
      <p style="font-size:13px; color:#c2410c; font-weight:600;">⚠️ Preuve de virement jointe — à vérifier dans le dashboard</p>
    </div>
    @endif

    <div style="text-align:center">
      <a href="{{ config('app.url') }}/admin" class="action-btn">
        Voir dans le Dashboard →
      </a>
    </div>
  </div>

  <div class="footer">
    <p>Email automatique · <strong>Ferme Khadija</strong> · Casablanca, Maroc</p>
    <p style="margin-top:6px">Réservation soumise le {{ $reservation->created_at->format('d/m/Y à H:i') }}</p>
  </div>
</div>
</body>
</html>