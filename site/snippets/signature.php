<?php
/**
 * Viv'assur - Email Signature Snippet
 * 
 * Optimized for cross-client compatibility:
 * - Nested table layouts with explicit pixel widths
 * - Strict inline CSS only
 * - Outlook (Word engine) compatibility (mso-table-lspace, mso-line-height-rule)
 * - iOS Mail & Gmail protection against auto-linking / blue phone numbers
 * - Explicit image dimensions and bicubic interpolation
 * - Hosted publicly:
 *   - Assets: https://www.vivassur.be/mail-signature/
 *   - Pictures: https://www.vivassur.be/mail-signature/pictures/
 */

$assetsBaseUrl = option('signature.assets_url', 'https://www.vivassur.be/mail-signature/assets');
$picturesBaseUrl = option('signature.pictures_url', 'https://www.vivassur.be/mail-signature/pictures');

// Determine profile photo URL
$photo = $signature->picture()->toFile() ?? $signature->images()->first();

if ($photo) {
    $photoUrl = $picturesBaseUrl . '/' . $photo->filename();
} else {
    $photoUrl = $assetsBaseUrl . '/default-photo.png';
}

$fullname = $signature->firstname()->html() . ' ' . $signature->lastname()->html();
$formattedLastname = mb_strtoupper($signature->lastname()->value(), 'UTF-8');

// Button formatting: bold domain part
$buttonText = $signature->website_label()->isNotEmpty() 
    ? $signature->website_label()->value() 
    : 'Rendez-vous sur www.vivassur.be';
$buttonUrl = $signature->website_url()->isNotEmpty() 
    ? $signature->website_url()->value() 
    : 'https://www.vivassur.be';

if (str_contains($buttonText, 'www.')) {
    $parts = explode('www.', $buttonText, 2);
    $formattedBtn = html($parts[0]) . '<strong>www.' . html($parts[1]) . '</strong>';
} elseif (preg_match('/^(.*?)(vivassur\.[a-z]+)(.*)$/i', $buttonText, $m)) {
    $formattedBtn = html($m[1]) . '<strong>' . html($m[2]) . '</strong>' . html($m[3]);
} else {
    $formattedBtn = html($buttonText);
}

// Select right decoration image according to radio field 'displayed_logo'
$selectedLogo = $signature->displayed_logo()->or($signature->logo_affiche())->or($signature->logo())->or('votre-courtier')->value();

if ($selectedLogo === 'caspea') {
    $rightDecFile = 'right-decoration-caspea.png';
    $rightDecAlt = 'Viv\'assur - Caspea';
} else {
    $rightDecFile = 'right-decoration-votre-courtier.png';
    $rightDecAlt = 'Viv\'assur - Votre Courtier';
}

$rightDecUrl = $assetsBaseUrl . '/' . $rightDecFile;
$facebookUrl = $assetsBaseUrl . '/facebook.png';
$instagramUrl = $assetsBaseUrl . '/instagram.png';
$linkedinUrl = $assetsBaseUrl . '/linkedin.png';
?>
<table cellpadding="0" cellspacing="0" border="0" style="vertical-align: -webkit-baseline-middle; font-family: Montserrat, Arial, Helvetica, sans-serif; width: 600px; min-width: 600px; max-width: 600px; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff;">
    <tr>
        <!-- 1. Colonne de gauche : Photo de profil (182px) -->
        <td width="182" valign="top" style="width: 182px; min-width: 182px; max-width: 182px; vertical-align: top; padding: 0; margin: 0; line-height: 0;">
            <img src="<?= $photoUrl ?>" width="182" height="200" alt="<?= $fullname ?>" style="display: block; width: 182px; height: 200px; border: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; object-fit: cover;" />
        </td>

        <!-- 2. Colonne centrale : Informations textuelles (273px) -->
        <td width="273" valign="top" style="width: 273px; min-width: 273px; max-width: 273px; vertical-align: top; padding: 0 0 0 25px; margin: 0; font-family: Montserrat, Arial, Helvetica, sans-serif;">
            <table cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                <!-- Espace supérieur -->
                <tr>
                    <td height="20" style="height: 20px; font-size: 1px; line-height: 1px; mso-line-height-rule: exactly;">&nbsp;</td>
                </tr>

                <!-- Nom & Prénom -->
                <tr>
                    <td style="font-family: Montserrat, Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; line-height: 21px; mso-line-height-rule: exactly; color: #011266; padding: 0 0 2px 0;">
                        <span style="color: #011266; font-family: Montserrat, Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; line-height: 21px; letter-spacing: -0.2px;">
                            <?= $signature->firstname()->html() ?> <?= $formattedLastname ?>
                        </span>
                    </td>
                </tr>

                <!-- Rôle / Fonction -->
                <tr>
                    <td style="font-family: Montserrat, Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; line-height: 15px; mso-line-height-rule: exactly; color: #0066ff; padding: 0 0 13px 0;">
                        <span style="color: #0066ff; font-family: Montserrat, Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; line-height: 15px;">
                            <?= $signature->role()->html() ?>
                        </span>
                    </td>
                </tr>

                <!-- Email -->
                <?php if ($signature->email()->isNotEmpty()): ?>
                <tr>
                    <td style="font-family: Montserrat, Arial, Helvetica, sans-serif; font-size: 12px; line-height: 14px; mso-line-height-rule: exactly; color: #0066ff; padding: 0 0 12px 0;">
                        <a href="mailto:<?= $signature->email()->value() ?>" style="color: #0066ff; text-decoration: none; font-family: Montserrat, Arial, Helvetica, sans-serif; font-size: 12px;">
                            <span style="color: #0066ff; text-decoration: none; font-family: Montserrat, Arial, Helvetica, sans-serif; font-size: 12px;"><?= $signature->email()->html() ?></span>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- Adresse & Téléphone -->
                <tr>
                    <td style="font-family: Montserrat, Arial, Helvetica, sans-serif; font-size: 11px; line-height: 13px; mso-line-height-rule: exactly; color: #011266; padding: 0 0 11px 0;">
                        <a href="https://www.vivassur.be/aide-contact/#gmap" target="_blank" style="color: #011266; text-decoration: none; display: block;">
                            <?php 
                            $addressText = $signature->address()->isNotEmpty() 
                                ? $signature->address()->value() 
                                : "Domaine de Séroule\nRue de Franchimont 1,\n4802 Verviers";
                            $addressLines = preg_split('/\r\n|\r|\n/', trim($addressText));
                            foreach ($addressLines as $line):
                                if (trim($line) !== ''):
                            ?>
                                <span style="display: block; color: #011266; font-size: 11px; line-height: 13px; font-family: Montserrat, Arial, Helvetica, sans-serif; mso-line-height-rule: exactly; text-decoration: none;"><?= html(trim($line)) ?></span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </a>
                        <?php if ($signature->phone()->isNotEmpty()): ?>
                            <span style="display: block; color: #011266; font-size: 11px; line-height: 13px; font-family: Montserrat, Arial, Helvetica, sans-serif; mso-line-height-rule: exactly;">
                                <a href="tel:<?= preg_replace('/[^0-9+]/', '', $signature->phone()->value()) ?>" style="color: #011266; text-decoration: none;"><span style="color: #011266; text-decoration: none;"><?= $signature->phone()->html() ?></span></a>
                            </span>
                        <?php endif; ?>
                        <?php if ($signature->mobile()->isNotEmpty()): ?>
                            <span style="display: block; color: #011266; font-size: 11px; line-height: 13px; font-family: Montserrat, Arial, Helvetica, sans-serif; mso-line-height-rule: exactly;">
                                <a href="tel:<?= preg_replace('/[^0-9+]/', '', $signature->mobile()->value()) ?>" style="color: #011266; text-decoration: none;"><span style="color: #011266; text-decoration: none;"><?= $signature->mobile()->html() ?></span></a>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Ligne bouton web + Réseaux sociaux -->
                <tr>
                    <td style="padding: 0;">
                        <table cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                            <tr>
                                <!-- Bouton pilule bleu -->
                                <td style="vertical-align: middle;">
                                    <table cellpadding="0" cellspacing="0" border="0" style="border-collapse: separate;">
                                        <tr>
                                            <td align="center" style="background-color: #0066ff; border-radius: 12px; padding: 3px 9px; font-family: Montserrat, Arial, Helvetica, sans-serif; font-size: 8.5px; line-height: 11px; mso-line-height-rule: exactly; text-align: center;">
                                                <a href="<?= $buttonUrl ?>" target="_blank" style="color: #ffffff; text-decoration: none; font-size: 8.5px; font-family: Montserrat, Arial, Helvetica, sans-serif; display: inline-block; white-space: nowrap;">
                                                    <span style="color: #ffffff; text-decoration: none; font-family: Montserrat, Arial, Helvetica, sans-serif; font-size: 8.5px;"><?= $formattedBtn ?></span>
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                                <!-- Icônes réseaux sociaux -->
                                <?php if ($signature->facebook()->isNotEmpty()): ?>
                                <td width="6" style="width: 6px; font-size: 1px; line-height: 1px;">&nbsp;</td>
                                <td style="vertical-align: middle; line-height: 0;">
                                    <a href="<?= $signature->facebook()->value() ?>" target="_blank" style="text-decoration: none; display: inline-block;">
                                        <img src="<?= $facebookUrl ?>" width="16" height="16" alt="Facebook" style="display: block; width: 16px; height: 16px; border: 0; outline: none; text-decoration: none;" />
                                    </a>
                                </td>
                                <?php endif; ?>

                                <?php if ($signature->instagram()->isNotEmpty()): ?>
                                <td width="6" style="width: 6px; font-size: 1px; line-height: 1px;">&nbsp;</td>
                                <td style="vertical-align: middle; line-height: 0;">
                                    <a href="<?= $signature->instagram()->value() ?>" target="_blank" style="text-decoration: none; display: inline-block;">
                                        <img src="<?= $instagramUrl ?>" width="16" height="16" alt="Instagram" style="display: block; width: 16px; height: 16px; border: 0; outline: none; text-decoration: none;" />
                                    </a>
                                </td>
                                <?php endif; ?>

                                <?php if ($signature->linkedin()->isNotEmpty()): ?>
                                <td width="6" style="width: 6px; font-size: 1px; line-height: 1px;">&nbsp;</td>
                                <td style="vertical-align: middle; line-height: 0;">
                                    <a href="<?= $signature->linkedin()->value() ?>" target="_blank" style="text-decoration: none; display: inline-block;">
                                        <img src="<?= $linkedinUrl ?>" width="16" height="16" alt="LinkedIn" style="display: block; width: 16px; height: 16px; border: 0; outline: none; text-decoration: none;" />
                                    </a>
                                </td>
                                <?php endif; ?>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>

        <!-- 3. Colonne de droite : Décoration arc bleu avec logo courtier / caspea (145px) -->
        <td width="145" valign="top" align="right" style="width: 145px; min-width: 145px; max-width: 145px; vertical-align: top; text-align: right; padding: 0; margin: 0; line-height: 0;">
            <img src="<?= $rightDecUrl ?>" width="145" height="200" alt="<?= $rightDecAlt ?>" style="display: block; width: 145px; height: 200px; border: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;" />
        </td>
    </tr>
</table>
