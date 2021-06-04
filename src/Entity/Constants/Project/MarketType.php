<?php

namespace App\Entity\Constants\Project;

class MarketType
{
    const WORK_ON_EXISTING = 'work_on_existing'; // Travaux sur existant
    const TCE = 'tce'; // T.C.E
    const NEW_BUILDING = 'new_building'; // Bât neuf
    const AGRICULTURAL_BUILDING = 'agricultural_building'; // Bât agricole
    const SHADES = 'shades'; // Ombrières
    const CALL_FOR_TENDER = 'call_for_tender'; // Appel d'offre
    const ISOTHERMAL_PLAN = 'isothermal_plan';  // Plan isotermes
    const SIMPLE_SUPPLY = 'simple_supply'; // Fourniture simple
    const ASBESTOS_REMOVAL = 'asbestos_removal'; // Désamiantage
    const ISOTHERMAL_PANELS = 'isothermal_panels'; // Panneaux isothermes
    const PUBLIC_MARKET = 'public_market'; // marche publc
    const PRIVATE_MARKET = 'private_market'; // marche privé
    const PUBLIC_AO = 'public_ao'; // A.O public
    const PRIVATE_AO = 'private_ao'; // A.O privé

    const MARKET_TYPES_CHOICES = [
        self::WORK_ON_EXISTING => self::WORK_ON_EXISTING,
        self::TCE => self::TCE,
        self::NEW_BUILDING => self::NEW_BUILDING,
        self::AGRICULTURAL_BUILDING => self::AGRICULTURAL_BUILDING,
        self::SHADES => self::SHADES,
        self::CALL_FOR_TENDER => self::CALL_FOR_TENDER,
        self::ISOTHERMAL_PLAN => self::ISOTHERMAL_PLAN,
        self::SIMPLE_SUPPLY => self::SIMPLE_SUPPLY,
        self::ASBESTOS_REMOVAL => self::ASBESTOS_REMOVAL,
    ];

    const MARKET_TYPE_2_CHOICES = [
        self::PUBLIC_MARKET => self::PUBLIC_MARKET,
        self::PRIVATE_MARKET => self::PRIVATE_MARKET,
        self::PUBLIC_AO => self::PUBLIC_AO,
        self::PRIVATE_AO => self::PRIVATE_AO,
    ];

    const CONTRACT_REVIEW_MARKET_TYPES_CHOICES = [
        self::WORK_ON_EXISTING => self::WORK_ON_EXISTING,
        self::TCE => self::TCE,
        self::SHADES => self::SHADES,
        self::AGRICULTURAL_BUILDING => self::AGRICULTURAL_BUILDING,
        self::CALL_FOR_TENDER => self::CALL_FOR_TENDER,
        self::ISOTHERMAL_PANELS => self::ISOTHERMAL_PANELS,
        self::NEW_BUILDING => self::NEW_BUILDING,
    ];
}