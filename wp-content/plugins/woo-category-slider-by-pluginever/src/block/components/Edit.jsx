import { sanitize } from 'dompurify'
const { __, setLocaleData } = wp.i18n;
const { Component, Fragment, createRef } = wp.element;
const { Placeholder, Spinner, PanelBody, SelectControl } = wp.components;
const { InspectorControls } = wp.editor;

class Edit extends Component {
    state = {
        sliders: {},
        loadingSliderList: true,
        htmlView: '',
        sliderInit: false,
        loadingSliderView: true,
    }
    container = createRef()
    componentDidMount() {
        const { attributes } = this.props;
        this.getSliders();

        if ( attributes.slider !==  0 && this.state.htmlView === '' ) {
            this.getSliderView();
        }

    }
    async getSliders() {
        const sliders = await wp.apiFetch( {
            path: 'wc-category-slider/v1/slider/all'
        } );

        this.setState( {
            sliders: sliders.success !== undefined && sliders.success === true ? sliders.data : {},
            loadingSliderList: false,
        } )
    }
    async getSliderView() {
        const { attributes } = this.props;
        const slider_view = await wp.apiFetch( {
            path: 'wc-category-slider/v1/slider/' + attributes.slider,
        } );

        this.setState( {
            htmlView: slider_view.success !== undefined && slider_view.success === true ? slider_view.data : '',
            loadingSliderView: false
        } );
    }
    componentDidUpdate( prevProps ) {
        const { attributes } = this.props;

        if ( attributes.slider !==  0 && this.state.htmlView === '' ) {
            this.getSliderView();
        }

        if ( this.state.htmlView !== '' && ! this.state.sliderInit  ) {
            setTimeout( () => {
                jQuery.wc_category_slider_public.init();
            }, 100 );

            this.setState( {
                sliderInit: true,
            } )
        }

        if ( attributes.align !== prevProps.attributes.align && this.state.htmlView !== '' && this.state.sliderInit ) {
            jQuery.wc_category_slider_public.reInit();
        }
    }
    render() {
        const { attributes, setAttributes } = this.props;
        const { sliders, loadingSliderList, htmlView, loadingSliderView } = this.state;

        let options = Object.keys( sliders ).map( ( slider_id ) => ( { value: slider_id, label: sliders[ slider_id ] } ) );

        options = [
            {
                value: 0,
                label: __( '--- Select a slider ---', 'woo-category-slider-by-pluginever' )
            },
            ...options
        ]

        return (
            <Fragment>
                <InspectorControls>
                    <PanelBody title={ __( 'Slider Settings' ) }>
                    <SelectControl
                        label={ esc_html__( 'Slider ID', 'woo-category-slider-by-pluginever' ) }
                        options={ options }
                        value={ attributes.slider }
                        onChange={ ( newValue ) => {

                            this.setState( {
                                htmlView: '',
                                sliderInit: false,
                                loadingSliderView: true,
                            } );

                            setAttributes( {
                                slider: parseInt( newValue ),
                            } );
                        } } />
                    </PanelBody>
                </InspectorControls>
                {
                    attributes.slider ===  0 ?
                    <Placeholder
                        icon="images-alt"
                        label={ __( 'WooCommerce Category Slider', 'woo-category-slider-by-pluginever' ) }>
                        {
                            loadingSliderList ?
                            <Spinner />
                            :
                            <SelectControl
                                options={ options }
                                onChange={ ( newValue ) => {

                                    setAttributes( {
                                        slider: parseInt( newValue ),
                                    } );
                                } } />
                        }
                    </Placeholder>
                    :
                    <Fragment>
                        {
                            loadingSliderView ?
                                <Placeholder
                                    icon="images-alt"
                                    label={ __( 'WooCommerce Category Slider', 'woo-category-slider-by-pluginever' ) }>
                                    <Spinner />
                                </Placeholder>
                                :
                                <div style={ {
                                    paddingTop: '1px',
                                } } className="wc-category-slider-view-container" dangerouslySetInnerHTML={ { __html: sanitize( htmlView ) } } />
                        }
                    </Fragment>
                }
            </Fragment>
        )
    }
}

export default Edit;
