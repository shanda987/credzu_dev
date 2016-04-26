<?php
if( !function_exists('AE_Private_Message_Actions') ) {
    /**
     * get instance of class AE_Private_Message_Actions
     *
     * @param void
     * @return object AE_Private_Message_Actions
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function AE_Private_Message_Actions()
    {
        return AE_Private_Message_Actions::getInstance();
    }
}
if( !function_exists('AE_AE_Message_Posttype') ) {
    /**
     * get instance of class AE_AE_Message_Posttype
     *
     * @param void
     * @return object mJobExtraAction
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function AE_AE_Message_Posttype()
    {
        return AE_AE_Message_Posttype::getInstance();
    }
}