<?php

namespace SynerBay;

class Ajax extends WPActionLoader
{
    /** @var \OfferApply */
    private $offerApplyModule;

    public function __construct()
    {
        include_once __DIR__ . '/backend/modules/OfferApply.php';
        $this->offerApplyModule = new \OfferApply();

        $this->addRestRoute('appearOffer', 'appear_offer');
        $this->addRestRoute('disAppearOffer', 'disappear_offer');
    }

    public function appearOffer()
    {
        $this->checkLogin();

        $post_data = wp_unslash($_POST);

        $offerID = isset($post_data['offerID']) ? sanitize_text_field($post_data['offerID']) : null;
        $productQty = isset($post_data['productQty']) ? sanitize_text_field($post_data['productQty']) : null;

        $this->responseSuccess($this->offerApplyModule->appearOfferForUser($this->getCurrentUserID(), $offerID,
            $productQty));
    }

    public function disAppearOffer()
    {
        $this->checkLogin();

        $post_data = wp_unslash($_POST);

        $offerID = isset($post_data['offerID']) ? sanitize_text_field($post_data['offerID']) : null;

        $this->responseSuccess($this->offerApplyModule->disAppearOfferForUser($this->getCurrentUserID(), $offerID));
    }

    /**
     * @return false|int
     */
    private function checkLogin()
    {
        if (!$this->getCurrentUserID()) {
            $this->getPleaseLoginResponse();
        }
    }

    private function getCurrentUserID()
    {
        return get_current_user_id();
    }

    private function getPleaseLoginResponse()
    {
        wp_send_json_error([], 401);
    }

    private function responseSuccess(array $responseData): void
    {
        wp_send_json_success($responseData, 200);
    }

    /**
     * @return false|\WP_User|null
     */
    public function getCurrentUser()
    {
        if (!$this->getCurrentUserID()) {
            return false;
        }

        return get_userdata($this->getCurrentUserID());
    }
}