<?php

namespace App\Entity\Constants\Project;

class FolderType
{
    const EARTHWORKS = 'earthworks'; // Terrassement
    const FRAME = 'frame'; // Charpente
    const STRUCTURAL_WORK = 'structural_work'; // 'Gros oeuvre'
    const COVERAGE = 'coverage'; // 'Couverture'
    const CVC_PLUMBING = 'cvc_plumbing'; // 'CVC plomberie'
    const BARDAGE = 'bardage'; // 'Cladding'
    const ELECTRICITY = 'electricity'; // 'Electricité'
    const LOCKSMITH = 'locksmith'; // 'Serrurerie'
    const INDOOR_LOTS = 'indoor_lots'; // 'Lots intérieurs'
    const PAN_ISOTHERMES = 'pan_isothermes'; // 'Pan. Isothermes'
    const ADMINISTRATIVE_FILE = 'administrative_file'; // 'Dossier administratif'
    const ASBESTOS_REMOVAL = 'asbestos_removal'; // 'Désamiantage'

    const FOLDER_TYPE_CHOICES = [
        self::EARTHWORKS => self::EARTHWORKS,
        self::FRAME => self::FRAME,
        self::STRUCTURAL_WORK => self::STRUCTURAL_WORK,
        self::COVERAGE => self::COVERAGE,
        self::CVC_PLUMBING => self::CVC_PLUMBING,
        self::BARDAGE => self::BARDAGE,
        self::ELECTRICITY => self::ELECTRICITY,
        self::LOCKSMITH => self::LOCKSMITH,
        self::INDOOR_LOTS => self::INDOOR_LOTS,
        self::PAN_ISOTHERMES => self::PAN_ISOTHERMES,
        self::ADMINISTRATIVE_FILE => self::ADMINISTRATIVE_FILE,
        self::ASBESTOS_REMOVAL => self::ASBESTOS_REMOVAL,
    ];
}