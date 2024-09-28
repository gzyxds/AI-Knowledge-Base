import type { WidgetMeta } from '../material'
import { createField, createIsRequired, createOptions, createTitle } from './_public'

const meta: WidgetMeta = {
    name: 'WidgetCheckbox',
    title: '多选',
    props: [
        createField(),
        createTitle(),
        createOptions(),
        {
            name: 'defaultValue',
            label: '默认值',
            type: 'array',
            setter: {
                name: 'Select',
                props: {
                    'get:options'(props: any) {
                        return props.options
                    },
                    clearable: true,
                    multiple: true
                }
            }
        },
        createIsRequired()
    ],
    sort: 5
}

export default meta
