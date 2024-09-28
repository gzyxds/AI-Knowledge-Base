import type { WidgetMeta } from '../material'
import { createField, createIsRequired, createOptions, createTitle } from './_public'
const meta: WidgetMeta = {
    name: 'WidgetSelect',
    title: '下拉选项',
    props: [
        createField(),
        createTitle(),
        createOptions(),
        {
            name: 'defaultValue',
            label: '默认值',
            type: 'string',
            setter: {
                name: 'Select',
                props: {
                    'get:options'(props: any) {
                        return props.options
                    },
                    clearable: true
                }
            }
        },
        createIsRequired()
    ],
    sort: 3
}

export default meta
