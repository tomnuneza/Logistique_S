<?php
namespace App\Enum;

enum EtatCommande: string {
  case EN_COURS = "en cours";
  case EN_INSTANCE = "en instance";
  case EN_PREPA = "en preparation";
  case EN_ATTENTE_EXPEDITION = "En attente d'expedition";
  case EXPEDIEE = "Expediee";
}