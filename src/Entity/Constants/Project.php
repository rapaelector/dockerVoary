<?php

namespace App\Entity\Constants;

class Project
{
// type marche
const TYPE_MARCHE_SHADOWS = 'typeMarket.shadows';
const TYPE_MARCHE_TENDER = 'typeMarket.tender';
const TYPE_MARCHE_T_C_E = 'typeMarket.tce';
const TYPE_MARCHE_NEW_BAT = 'typeMarket.new_bat';
const TYPE_MARCHE_AGRICULTURAL_BAT = 'typeMarket.agricultural_bat';
const TYPE_MARCHE_ISOTERMAL_PANEL = 'typeMarket.isotermal_bat';
const TYPE_MARCHE_MARCHE_PUBLIC = 'typeMarket.public_market';
const TYPE_MARCHE_MARCHE_PRIVATE = 'typeMarket.private_market';
const TYPE_MARCHE_A_O_PUBLIC = 'typeMarket.ao_public';
const TYPE_MARCHE_AO_PRIVE = 'typeMarket.ao_private';

// bonhome est il
const TYPE_BONHOMME_CONTRACT_HOLDER = "bonhommePercentage.contract_holder";
const TYPE_BONHOMME_20PERCENT = "bonhommePercentage.20percent";
const TYPE_BONHOMME_10PERCENT = "bonhommePercentage.10percent";
const TYPE_BONHOMME_SUBCONTRACTOR = "bonhommePercentage.subcontractor";
const TYPE_BONHOMME_0PERCENT = "bonhommePercentage.0percent";

// validation de la fiche DISA
const TYPE_DISA_SHEET_SIGNED_QUOTE = "disaSheetValidation.signed_quote";
const TYPE_DISA_SHEET_CUSTOMER_ORDER_FORM = "disaSheetValidation.customer_order_form";
const TYPE_DISA_SHEET_AUTHORIZATION_LETTER = "disaSheetValidation.authorization_letter";
const TYPE_DISA_SHEET_SUBCONTRACT = "disaSheetValidation.subcontract";

// mode de reglement

const PAYMENT_TYPE_CARD = "payment.type.card";
const PAYMENT_TYPE_CASH = "payment.type.cash";
const PAYMENT_TYPE_TRANSFER = "payment.type.transfer";
const PAYMENT_TYPE_CHECK = "payment.type.check";

// type de dossier
const CASE_TYPE_EARTH_WORKS = "caseType.earthWorks";
const CASE_TYPE_BIG_WORK = "caseType.bigWork";
const CASE_TYPE_PLUMBING = "caseType.plumbing";
const CASE_TYPE_ELECTRICITY = "caseType.electricity";
const CASE_TYPE_INDOOR_LOTS = "caseType.indoorLots";
const CASE_TYPE_ADMIN_FILE = "caseType.adminFile";
const CASE_TYPE_FRAME = "caseType.frame";
const CASE_TYPE_BLANKET = "caseType.blanket";
const CASE_TYPE_CLADDING = "caseType.cladding";
const CASE_TYPE_LOCKSMITH = "caseType.locksmith";
const CASE_TYPE_ISOTHERMES = "caseType.isothermes";
const CASE_TYPE_ASBESTOS_REMOVAL = "caseType.asbestosRemoval";

const CASE_TYPES = [
    self::CASE_TYPE_EARTH_WORKS,
    self::CASE_TYPE_BIG_WORK,
    self::CASE_TYPE_PLUMBING,
    self::CASE_TYPE_ELECTRICITY,
    self::CASE_TYPE_INDOOR_LOTS,
    self::CASE_TYPE_ADMIN_FILE,
    self::CASE_TYPE_FRAME,
    self::CASE_TYPE_BLANKET,
    self::CASE_TYPE_CLADDING,
    self::CASE_TYPE_LOCKSMITH,
    self::CASE_TYPE_ISOTHERMES,
    self::CASE_TYPE_ASBESTOS_REMOVAL,
];

const PAYMENT_TYPES =  [
    self::PAYMENT_TYPE_CHECK,
    self::PAYMENT_TYPE_TRANSFER,
];

const TYPE_DE_MARCHE = [
    self::TYPE_MARCHE_SHADOWS,
    self::TYPE_MARCHE_TENDER,
    self::TYPE_MARCHE_T_C_E,
    self::TYPE_MARCHE_NEW_BAT,
    self::TYPE_MARCHE_AGRICULTURAL_BAT,
    self::TYPE_MARCHE_ISOTERMAL_PANEL,
    self::TYPE_MARCHE_MARCHE_PUBLIC,
    self::TYPE_MARCHE_MARCHE_PRIVATE,
    self::TYPE_MARCHE_A_O_PUBLIC,
    self::TYPE_MARCHE_AO_PRIVE,
];

const TYPE_BONHOME = [
    self::TYPE_BONHOMME_CONTRACT_HOLDER => [
        self::TYPE_BONHOMME_20PERCENT => self::TYPE_BONHOMME_20PERCENT,
        self::TYPE_BONHOMME_10PERCENT => self::TYPE_BONHOMME_10PERCENT,
    ],
    self::TYPE_BONHOMME_SUBCONTRACTOR => [
        self::TYPE_BONHOMME_0PERCENT => self::TYPE_BONHOMME_0PERCENT,
    ]
];

const TYPE_DISA_SHEET = [
    self::TYPE_DISA_SHEET_SIGNED_QUOTE,
    self::TYPE_DISA_SHEET_CUSTOMER_ORDER_FORM,
    self::TYPE_DISA_SHEET_AUTHORIZATION_LETTER,
    self::TYPE_DISA_SHEET_SUBCONTRACT,
];

public static function getTypeValues($values, $associative = false)
{
    return $associative ? array_combine($values, $values) : $values;
}

public static function getTypeBonhomme()
{
    return self::TYPE_BONHOME;
}

}