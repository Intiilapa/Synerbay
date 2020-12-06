<?php

class OfferApply
{
    /** @var Offer $offerModule */
    private Offer $offerModule;

    public function __construct()
    {
        include_once 'Offer.php';
        $this->offerModule = new Offer();
    }

    public function appearOfferForUser(int $userID, int $offerID, int $productQuantity)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'offer_applies';
        $data = [
            'user_id'  => $userID,
            'offer_id' => $offerID,
            'qty'      => $productQuantity,
        ];
        $format = ['%d', '%d', '%d'];

        try {
            $wpdb->insert($table, $data, $format);
        } catch (Exception $e) {
            return false;
        }

        // TODO mail-ek kiküldése

        return $this->offerModule->getOfferCurrentData($offerID);
    }

    public function disAppearOfferForUser(int $userID, int $offerID)
    {
        global $wpdb;

        try {
            $wpdb->delete('sb_offer_applies', [
                'offer_id' => $offerID,
                'user_id' => $userID,
            ]);
        } catch (Exception $e) {
            return false;
        }

        // TODO mail-ek kiküldése
        return $this->offerModule->getOfferCurrentData($offerID);
    }

    /**
     * A user már jelentkezett az ajánlatra?
     *
     * @param int $userID
     * @param int $offerID
     * @return int
     */
    public function isUserAppliedOffer(int $userID, int $offerID)
    {
        global $wpdb;
        $result = $wpdb->get_results('select count(id) as count from sb_offer_applies WHERE user_id = ' . $userID . ' and offer_id = ' . $offerID);

        return (bool)$result[0]->count;
    }
}