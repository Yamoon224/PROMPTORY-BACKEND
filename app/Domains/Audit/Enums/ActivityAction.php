<?php

namespace App\Domains\Audit\Enums;

/** Evenements d'usage traces par le domaine, au sens analytics du cahier des charges. */
enum ActivityAction: string
{
    case View = 'view';
    case Download = 'download';
    case Purchase = 'purchase';
    case Edit = 'edit';
    case SaveFolder = 'save_folder';
    case SubmitValidation = 'submit_validation';
    case Approve = 'approve';
    case Reject = 'reject';
}
