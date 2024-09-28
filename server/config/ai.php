<?php

return [
    // 对话模型
    'ChatModels' => [
        // 文档: https://platform.openai.com/docs/models
        'openai' => [
            'logo' => 'resource/image/models/gpt3.5.png',
            'name' => 'OpenAI',
            'website' => 'https://platform.openai.com',
            'configs' => [
                array (
                    'type'     => 'group',
                    'name'    => '参数设置',
                    'require' => true,
                    'config'  => [
                        [
                            'key'     => 'context_num',
                            'name'    => '上下文数',
                            'type'    => 'slider',
                            'default' => 3,
                            'range'   => [0, 5],
                            'step'    => 1,
                            'tips'    => '生成文本的最大长度，取值范围为1~5之间的整数'
                        ],
                        [
                            'key'     => 'temperature',
                            'name'    => '词汇属性',
                            'type'    => 'slider',
                            'default' => 1.0,
                            'range'   => [0, 2.0],
                            'step'    => 0.1,
                            'tips'    =>'用于控制生成文本的随机性，取值范围为0~2之间的浮点数，建议取值1.0左右。'
                        ],
                        [
                            'key'     => 'presence_penalty',
                            'name'    => '存在惩罚',
                            'type'    => 'slider',
                            'default' => 0.0,
                            'range'   => [-2.0, 2.0],
                            'step'    => 0.1,
                            'tips'    => '用于通过惩罚已经使用的词，增加模型谈论新主题的可能性'
                        ],
                        [
                            'key'     => 'frequency_penalty',
                            'name'    => '频率惩罚',
                            'type'    => 'slider',
                            'default' => 0.0,
                            'range'   => [-2.0, 2.0],
                            'step'    => 0.1,
                            'tips'    => '用于惩罚已经频繁使用的词来降低模型一行中重复用词的可能性'
                        ],
                        [
                            'key'     => 'check_key',
                            'name'    => '校验密钥',
                            'type'    => 'switch',
                            'default' => true,
                            'tips'    => '考虑到有些本地模型不需要key也可调用,故此增加校验开关,默认位false,表示不校验密钥。'
                        ]
                    ]
                ),
                [
                    'key'     => 'agency_api',
                    'name'    => '代理域名',
                    'type'    => 'input',
                    'default' => '',
                    'tips'    => '反向代理API域名，不填写默认为： https://api.openai.com'
                ],
                [
                    'key'     => 'global_directives',
                    'name'    => '对话指令',
                    'type'    => 'textarea',
                    'default' => '',
                    'tips'    => '设置全局指令，屏蔽审核人员询问模型类型等相关问题'
                ]
            ]
        ],
        // 文档: https://www.xfyun.cn/doc/spark/Web.html
        'xunfei' => [
            'logo' => 'resource/image/models/xunfei.png',
            'name' => '讯飞星火',
            'website' => 'https://www.xfyun.cn',
            'configs' => [
                array(
                    'type'    => 'group',
                    'name'    => '参数设置',
                    'require' => true,
                    'config' => [
                        [
                            'key'     => 'context_num',
                            'name'    => '上下文数',
                            'type'    => 'slider',
                            'default' => 3,
                            'range'   => [0, 5],
                            'step'    => 0.1,
                            'tips'    => '生成文本的最大长度，取值范围为1~5之间的整数'
                        ],
                        [
                            'key'     => 'temperature',
                            'name'    => '词汇属性',
                            'type'    => 'slider',
                            'default' => 0.5,
                            'range'   => [0, 1],
                            'step'    => 0.1,
                            'tips'    => '用于控制生成文本的随机性，取值范围为0~1之间的浮点数，建议取值0.5左右。'
                        ],
                        [
                            'key'     => 'top_k',
                            'name'    => '⾮等概率',
                            'type'    => 'slider',
                            'default' => 4,
                            'range'   => [1, 6],
                            'step'    => 0.1,
                            'tips'    => '从k个候选中随机选择⼀个'
                        ],
                    ]
                ),
                [
                    'key'     => 'agency_api',
                    'name'    => '代理域名',
                    'type'    => 'input',
                    'default' => '',
                    'tips'    => '反向代理API域名，不填写默认为科大讯飞方地址'
                ],
                [
                    'key'     => 'global_directives',
                    'name'    => '对话指令',
                    'type'    => 'textarea',
                    'default' => '',
                    'tips'    => '设置全局指令，屏蔽审核人员询问模型类型等相关问题'
                ]
            ]
        ],
        // 文档: https://open.bigmodel.cn/dev/api#chatglm_turbo
        'zhipu' => [
            'logo' => 'resource/image/models/zhipu.png',
            'name' => '智普清言',
            'website' => 'https://open.bigmodel.cn',
            'configs' => [
                array (
                    'type'    => 'group',
                    'name'    => '参数设置',
                    'require' => true,
                    'config'  => [
                        [
                            'key'     => 'context_num',
                            'name'    => '上下文数',
                            'type'    => 'slider',
                            'default' => 3,
                            'range'   => [0, 5],
                            'step'    => 0.1,
                            'tips'    => '生成文本的最大长度，取值范围为1~5之间的整数'
                        ],
                        [
                            'key'     => 'temperature',
                            'name'    => '词汇属性',
                            'type'    => 'slider',
                            'default' => 0.95,
                            'step'    => 0.1,
                            'range'   => [0, 1],
                            'tips'    =>'用于控制生成文本的随机性，取值范围为0~1之间的浮点数，建议取值0.95左右。'
                        ],
                        [
                            'key'     => 'do_sample',
                            'name'    => '采样策略',
                            'type'    => 'switch',
                            'default' => true,
                            'tips'    => '采样策略开启时temperature、top_p 将不生效'
                        ]
                    ]
                ),
                [
                    'key'     => 'agency_api',
                    'name'    => '代理域名',
                    'type'    => 'input',
                    'default' => '',
                    'tips'    => '反向代理API域名，不填写默: https://open.bigmodel.cn',
                    'placeholder'=> '请输入自定义API域名'
                ],
                [
                    'key'     => 'global_directives',
                    'name'    => '对话指令',
                    'type'    => 'textarea',
                    'default' => '',
                    'tips'    => '设置全局指令，屏蔽审核人员询问模型类型等相关问题'
                ]
            ]
        ],
        // 文档: https://cloud.baidu.com/doc/WENXINWORKSHOP/s/jlil56u11
        'baidu' => [
            'logo' => 'resource/image/models/baidu.png',
            'name' => '文心一言',
            'website' => 'https://qianfan.cloud.baidu.com',
            'configs' => [
                array (
                    'type'    => 'group',
                    'name'    => '参数设置',
                    'require' => true,
                    'config'  => [
                        [
                            'key'     => 'context_num',
                            'name'    => '上下文数',
                            'type'    => 'slider',
                            'default' => 3,
                            'range'   => [0, 5],
                            'step'    => 0.1,
                            'tips'    => '生成文本的最大长度，取值范围为1~5之间的整数'
                        ],
                        [
                            'key'     => 'temperature',
                            'name'    => '词汇属性',
                            'type'    => 'slider',
                            'default' => 0.8,
                            'range'   => [0.1, 1],
                            'step'    => 0.1,
                            'tips'    =>'用于控制生成文本的随机性，取值范围为0~1之间的浮点数，建议取值0.8左右。'
                        ],
                        [
                            'key'     => 'penalty_score',
                            'name'    => '重复惩罚',
                            'type'    => 'slider',
                            'default' => 1.0,
                            'range'   => [1.0, 2.0],
                            'step'    => 0.1,
                            'tips'    => '减少重复生成的现象, 值越大表示惩罚越大, 默认1.0'
                        ],
                        [
                            'key'     => 'disable_search',
                            'name'    => '关闭搜索',
                            'type'    => 'switch',
                            'default' => false,
                            'tips'    => '是否强制关闭实时搜索功能，默认false，表示不关闭'
                        ],
                    ]
                ),
                [
                    'key'     => 'agency_api',
                    'name'    => '代理域名',
                    'type'    => 'input',
                    'default' => '',
                    'tips'    => '反向代理API域名，不填写默认: https://aip.baidubce.com'
                ],
                [
                    'key'     => 'global_directives',
                    'name'    => '对话指令',
                    'type'    => 'textarea',
                    'default' => '',
                    'tips'    => '设置全局指令，屏蔽审核人员询问模型类型等相关问题'
                ]
            ]
        ],
        // 文档: https://help.aliyun.com/zh/dashscope/developer-reference/model-introduction?spm=a2c4g.11186623.0.0.2970140bFwsVfj
        'qwen'  => [
            'logo' => 'resource/image/models/qwen.png',
            'name' => '通义千问',
            'website' => 'https://help.aliyun.com/zh/dashscope/opening-service',
            'configs' => [
                array (
                    'type'    => 'group',
                    'name'    => '参数设置',
                    'require' => true,
                    'config'  => [
                        [
                            'key'     => 'context_num',
                            'name'    => '上下文数',
                            'type'    => 'slider',
                            'default' => 3,
                            'range'   => [0, 5],
                            'step'    => 0.1,
                            'tips'    => '生成文本的最大长度，取值范围为1~5之间的整数'
                        ],
                        [
                            'key'     => 'temperature',
                            'name'    => '词汇属性',
                            'type'    => 'slider',
                            'default' => 0.8,
                            'range'   => [0.1, 2],
                            'step'    => 0.1,
                            'tips'    =>'用于控制生成文本的随机性，取值范围为0~1之间的浮点数，建议取值0.8左右。'
                        ],
                        [
                            'key'     => 'repetition_penalty',
                            'name'    => '重复惩罚',
                            'type'    => 'slider',
                            'default' => 1.0,
                            'range'   => [1.0, 2.0],
                            'step'    => 0.1,
                            'tips'    => '减少重复生成的现象, 值越大表示惩罚越大, 默认1.0(不做惩罚)'
                        ],
                        [
                            'key'     => 'presence_penalty',
                            'name'    => '存在惩罚',
                            'type'    => 'slider',
                            'default' => 0.0,
                            'range'   => [-2.0, 2.0],
                            'step'    => 0.1,
                            'tips'    => '用于通过惩罚已经使用的词，增加模型谈论新主题的可能性'
                        ],
                        [
                            'key'     => 'enable_search',
                            'name'    => '互联网搜索',
                            'type'    => 'switch',
                            'default' => false,
                            'tips'    => '启用互联网搜索，模型会将搜索结果作为文本生成过程中的参考信息，默认false，表示关闭'
                        ],
                    ]
                ),
                [
                    'key'     => 'agency_api',
                    'name'    => '代理域名',
                    'type'    => 'input',
                    'default' => '',
                    'tips'    => '反向代理API域名，不填写默认: https://dashscope.aliyuncs.com'
                ],
                [
                    'key'     => 'global_directives',
                    'name'    => '对话指令',
                    'type'    => 'textarea',
                    'default' => '',
                    'tips'    => '设置全局指令，屏蔽审核人员询问模型类型等相关问题'
                ]
            ]
        ],
        // 文档: https://www.volcengine.com/product/doubao
        'doubao'  => [
            'logo' => 'resource/image/models/doubao.png',
            'name' => '字节豆包',
            'website' => 'https://www.volcengine.com/product/doubao',
            'configs' => [
                array (
                    'type'     => 'group',
                    'name'    => '参数设置',
                    'require' => true,
                    'config'  => [
                        [
                            'key'     => 'context_num',
                            'name'    => '上下文数',
                            'type'    => 'slider',
                            'default' => 3,
                            'range'   => [0, 5],
                            'step'    => 1,
                            'tips'    => '生成文本的最大长度，取值范围为1~5之间的整数.'
                        ],
                        [
                            'key'     => 'temperature',
                            'name'    => '词汇属性',
                            'type'    => 'slider',
                            'default' => 1.0,
                            'range'   => [0, 2.0],
                            'step'    => 0.1,
                            'tips'    =>'用于控制生成文本的随机性，取值范围为0~2之间的浮点数，建议取值1.0左右.'
                        ],
                        [
                            'key'     => 'frequency_penalty',
                            'name'    => '频率惩罚',
                            'type'    => 'slider',
                            'default' => 0.0,
                            'range'   => [-2.0, 2.0],
                            'step'    => 0.1,
                            'tips'    => '用于惩罚已经频繁使用的词来降低模型一行中重复用词的可能性'
                        ],
                        [
                            'key'     => 'check_key',
                            'name'    => '校验密钥',
                            'type'    => 'switch',
                            'default' => true,
                            'tips'    => '考虑到有些本地模型不需要key也可调用,故此增加校验开关,默认位false,表示不校验密钥。'
                        ]
                    ]
                ),
                [
                    'key'     => 'agency_api',
                    'name'    => '代理域名',
                    'type'    => 'input',
                    'default' => '',
                    'tips'    => '反向代理API域名，默认为: https://ark.cn-beijing.volces.com/api/v3'
                ],
                [
                    'key'     => 'global_directives',
                    'name'    => '对话指令',
                    'type'    => 'textarea',
                    'default' => '',
                    'tips'    => '设置全局指令，屏蔽审核人员询问模型类型等相关问题.'
                ]
            ]
        ],
        // 文档: https://ollama.com
        'ollama'  => [
            'logo' => 'resource/image/models/ollama.png',
            'name' => 'Ollama',
            'website' => 'https://ollama.com',
            'configs' => [
                array (
                    'type'    => 'group',
                    'name'    => '参数设置',
                    'require' => true,
                    'config'  => [
                        [
                            'key'     => 'context_num',
                            'name'    => '上下文数',
                            'type'    => 'slider',
                            'default' => 3,
                            'range'   => [0, 5],
                            'step'    => 1,
                            'tips'    => '生成文本的最大长度，取值范围为1~5之间的整数'
                        ],
                        [
                            'key'     => 'temperature',
                            'name'    => '词汇属性',
                            'type'    => 'slider',
                            'default' => 0.8,
                            'range'   => [0.1, 2],
                            'step'    => 0.1,
                            'tips'    =>'用于控制生成文本的随机性，取值范围为0~1之间的浮点数，建议取值0.8左右。'
                        ],
                        [
                            'key'     => 'repeat_penalty',
                            'name'    => '重复惩罚',
                            'type'    => 'slider',
                            'default' => 1.1,
                            'range'   => [0, 2.0],
                            'step'    => 0.1,
                            'tips'    => '减少重复生成的现象, 值越大表示惩罚越大, 默认: 1.1'
                        ],
                        [
                            'key'     => 'mirostat',
                            'name'    => '困惑控制',
                            'type'    => 'slider',
                            'default' => 0,
                            'range'   => [0, 2],
                            'step'    => 1,
                            'tips'    => '启用 Mirostat 采样以控制困惑度, 默认: 0'
                        ],
                        [
                            'key'     => 'mirostat_eta',
                            'name'    => '响应效率',
                            'type'    => 'slider',
                            'default' => 0.1,
                            'range'   => [0, 1],
                            'step'    => 0.01,
                            'tips'    => '影响算法对生成文本反馈的响应速度'
                        ],
                        [
                            'key'     => 'check_key',
                            'name'    => '校验密钥',
                            'type'    => 'switch',
                            'default' => false,
                            'tips'    => '考虑到有些本地模型不需要key也可调用,故此增加校验开关,默认位false,表示不校验密钥。'
                        ],
                    ]
                ),
                [
                    'key'     => 'agency_api',
                    'name'    => '代理域名',
                    'type'    => 'input',
                    'default' => '',
                    'tips'    => '请填写你本地部署服务的请求域名。'
                ],
                [
                    'key'     => 'global_directives',
                    'name'    => '对话指令',
                    'type'    => 'textarea',
                    'default' => '',
                    'tips'    => '设置全局指令，屏蔽审核人员询问模型类型等相关问题'
                ]
            ]
        ],
        // 文档: https://azure.microsoft.com/zh-cn/free/ai-services
        'azure'  => [
            'logo' => 'resource/image/models/azure.png',
            'name' => 'Azure',
            'website' => 'https://azure.microsoft.com/zh-cn/free/',
            'configs' => [
                array (
                    'type'     => 'group',
                    'name'    => '参数设置',
                    'require' => true,
                    'config'  => [
                        [
                            'key'     => 'context_num',
                            'name'    => '上下文数',
                            'type'    => 'slider',
                            'default' => 3,
                            'range'   => [0, 5],
                            'step'    => 1,
                            'tips'    => '生成文本的最大长度，取值范围为1~5之间的整数.'
                        ],
                        [
                            'key'     => 'temperature',
                            'name'    => '词汇属性',
                            'type'    => 'slider',
                            'default' => 1.0,
                            'range'   => [0, 2.0],
                            'step'    => 0.1,
                            'tips'    =>'用于控制生成文本的随机性，取值范围为0~2之间的浮点数，建议取值1.0左右。'
                        ],
                        [
                            'key'     => 'presence_penalty',
                            'name'    => '存在惩罚',
                            'type'    => 'slider',
                            'default' => 0.0,
                            'range'   => [-2.0, 2.0],
                            'step'    => 0.1,
                            'tips'    => '用于通过惩罚已经使用的词，增加模型谈论新主题的可能性'
                        ],
                        [
                            'key'     => 'frequency_penalty',
                            'name'    => '频率惩罚',
                            'type'    => 'slider',
                            'default' => 0.0,
                            'range'   => [-2.0, 2.0],
                            'step'    => 0.1,
                            'tips'    => '用于惩罚已经频繁使用的词来降低模型一行中重复用词的可能性'
                        ],
                        [
                            'key'     => 'check_key',
                            'name'    => '校验密钥',
                            'type'    => 'switch',
                            'default' => true,
                            'tips'    => '考虑到有些本地模型不需要key也可调用,故此增加校验开关,默认位false,表示不校验密钥。'
                        ]
                    ]
                ),
                [
                    'key'     => 'agency_api',
                    'name'    => '代理域名',
                    'type'    => 'input',
                    'default' => '',
                    'tips'    => '反向代理API域名，请填写您的服务地址'
                ],
                [
                    'key'     => 'global_directives',
                    'name'    => '对话指令',
                    'type'    => 'textarea',
                    'default' => '',
                    'tips'    => '设置全局指令，屏蔽审核人员询问模型类型等相关问题'
                ]
            ]
        ],
        // 文档: https://platform.minimaxi.com
        'minimax'  => [
            'logo' => 'resource/image/models/minimax.png',
            'name' => 'MiniMax',
            'website' => 'https://platform.minimaxi.com',
            'configs' => [
                array (
                    'type'     => 'group',
                    'name'    => '参数设置',
                    'require' => true,
                    'config'  => [
                        [
                            'key'     => 'context_num',
                            'name'    => '上下文数',
                            'type'    => 'slider',
                            'default' => 3,
                            'range'   => [0, 5],
                            'step'    => 1,
                            'tips'    => '生成文本的最大长度，取值范围为1~5之间的整数.'
                        ],
                        [
                            'key'     => 'temperature',
                            'name'    => '词汇属性',
                            'type'    => 'slider',
                            'default' => 1.0,
                            'range'   => [0, 2.0],
                            'step'    => 0.1,
                            'tips'    =>'用于控制生成文本的随机性，取值范围为0~2之间的浮点数，建议取值1.0左右。'
                        ],
                        [
                            'key'     => 'check_key',
                            'name'    => '校验密钥',
                            'type'    => 'switch',
                            'default' => true,
                            'tips'    => '考虑到有些本地模型不需要key也可调用,故此增加校验开关,默认位false,表示不校验密钥。'
                        ]
                    ]
                ),
                [
                    'key'     => 'agency_api',
                    'name'    => '代理域名',
                    'type'    => 'input',
                    'default' => '',
                    'tips'    => '反向代理API域名，不填写默认为：https://api.minimax.chat'
                ],
                [
                    'key'     => 'global_directives',
                    'name'    => '对话指令',
                    'type'    => 'textarea',
                    'default' => '',
                    'tips'    => '设置全局指令，屏蔽审核人员询问模型类型等相关问题'
                ]
            ]
        ],
        // 文档: https://platform.baichuan-ai.com
        'baichuan' => [
            'logo' => 'resource/image/models/baichuan.png',
            'name' => '百川智能',
            'website' => 'https://platform.baichuan-ai.com',
            'configs' => [
                array (
                    'type'     => 'group',
                    'name'    => '参数设置',
                    'require' => true,
                    'config'  => [
                        [
                            'key'     => 'context_num',
                            'name'    => '上下文数',
                            'type'    => 'slider',
                            'default' => 3,
                            'range'   => [0, 5],
                            'step'    => 1,
                            'tips'    => '生成文本的最大长度，取值范围为1~5之间的整数'
                        ],
                        [
                            'key'     => 'temperature',
                            'name'    => '词汇属性',
                            'type'    => 'slider',
                            'default' => 1.0,
                            'range'   => [0, 2.0],
                            'step'    => 0.1,
                            'tips'    =>'用于控制生成文本的随机性，取值范围为0~2之间的浮点数，建议取值1.0左右。'
                        ],
                        [
                            'key'     => 'presence_penalty',
                            'name'    => '存在惩罚',
                            'type'    => 'slider',
                            'default' => 1,
                            'range'   => [1.0, 2.0],
                            'step'    => 0.1,
                            'tips'    => '用于通过惩罚已经使用的词，增加模型谈论新主题的可能性'
                        ],
                        [
                            'key'     => 'frequency_penalty',
                            'name'    => '频率惩罚',
                            'type'    => 'slider',
                            'default' => 1,
                            'range'   => [1.0, 2.0],
                            'step'    => 0.1,
                            'tips'    => '用于惩罚已经频繁使用的词来降低模型一行中重复用词的可能性'
                        ],
                        [
                            'key'     => 'check_key',
                            'name'    => '校验密钥',
                            'type'    => 'switch',
                            'default' => true,
                            'tips'    => '考虑到有些本地模型不需要key也可调用,故此增加校验开关,默认位false,表示不校验密钥。'
                        ]
                    ]
                ),
                [
                    'key'     => 'agency_api',
                    'name'    => '代理域名',
                    'type'    => 'input',
                    'default' => '',
                    'tips'    => '反向代理API域名，不填写默认为：https://api.baichuan-ai.com'
                ],
                [
                    'key'     => 'global_directives',
                    'name'    => '对话指令',
                    'type'    => 'textarea',
                    'default' => '',
                    'tips'    => '设置全局指令，屏蔽审核人员询问模型类型等相关问题'
                ]
            ]
        ],
    ],
    // 向量模型
    'VectorModels' => [
        'openai' => [
            'logo'       => 'resource/image/models/openai.png',
            'name'       => 'OpenAI',
            'configs' => [
                [
                    'key'     => 'agency_api',
                    'name'    => '代理域名',
                    'type'    => 'input',
                    'default' => '',
                    'tips'    => '反向代理API域名，不填写默认: https://api.openai.com'
                ]
            ]
        ],
        'xunfei' => [
            'logo'       => 'resource/image/models/xunfei.png',
            'name'       => '讯飞星火',
            'configs' => [
                [
                    'key'     => 'agency_api',
                    'name'    => '代理域名',
                    'type'    => 'input',
                    'default' => '',
                    'tips'    => '反向代理API域名，不填写默认: https://emb-cn-huabei-1.xf-yun.com'
                ]
            ]
        ],
        'zhipu' => [
            'logo'       => 'resource/image/models/zhipu.png',
            'name'       => '智普清言',
            'configs' => [
                [
                    'key'     => 'agency_api',
                    'name'    => '代理域名',
                    'type'    => 'input',
                    'default' => '',
                    'tips'    => '反向代理API域名，不填写默认: https://open.bigmodel.cn'
                ]
            ]
        ],
        'qwen' => [
            'logo'    => 'resource/image/models/qwen.png',
            'name'    => '通义千问',
            'configs' => [
                [
                    'key'     => 'agency_api',
                    'name'    => '代理域名',
                    'type'    => 'input',
                    'default' => '',
                    'tips'    => '反向代理API域名，不填写默认: https://dashscope.aliyuncs.com'
                ]
            ]
        ]
    ],
    // 示例模型
    'ExampleModels' => [
        'chat' => [
            // GPT
            'gpt-3.5-turbo',
            'gpt-3.5-turbo-0125',
            'gpt-3.5-turbo-1106',
            'gpt-3.5-turbo-instruct',
            'gpt-4',
            'gpt-4-32k',
            'gpt-4-0613',
            'gpt-4-32k-0613',
            'gpt-4-turbo',
            'gpt-4-turbo-2024-04-09',
            'gpt-4-turbo-preview',
            'gpt-4-0125-preview',
            'gpt-4-1106-preview',
            'gpt-4-vision-preview',
            'gpt-4-1106-vision-preview',
            'gpt-4o',
            'gpt-4o-2024-05-13',
            'gpt-4o-mini',
            // 智普
            'glm-4',
            'glm-4v',
            'glm-3-turbo',
            // 百度
            'ERNIE-3.5-8K',
            'ERNIE-3.5-4K-0205',
            'ERNIE-3.5-8K-0205',
            'ERNIE-3.5-8K-1222',
            'ERNIE-3.5-8K-0329',
            'ERNIE-3.5-8K-Preview',
            'ERNIE-3.5-128K',
            'ERNIE-4.0-8K',
            'ERNIE-4.0-8K-0329',
            'ERNIE-4.0-8K-0104',
            'ERNIE-4.0-8K-Preview',
            'ERNIE-4.0-8K-Preview-0518',
            // 讯飞
            'pro-128k',
            '4.0Ultra',
            'generalv3',
            'generalv3.5',
            // 通义
            'qwen-turbo',
            'qwen-max',
            'qwen-plus',
            'qwen-max-longcontext',
            // ChatGLM
            'chatglm2-6b',
            'chatglm2-6b-32k',
            'chatglm2-6b-128k',
            'chatglm2-6b-32k-int4',
            'chatglm2-6b-32k-int8',
            'chatglm3-6b',
            'chatglm3-6b-32k',
            'chatglm3-6b-base',
            // Ollama
            'llama',
            'phi3',
            'gemma',
            'mistral',
            'moondream',
            'codellama',
            'llama2-uncensored',
            'llava',
            'solar',
            // deepseek
            'deepseek-chat',
            'deepseek-coder',
            // Kimi
            'moonshot-v1-8k',
            'moonshot-v1-32k',
            'moonshot-v1-128k',
            // MiniMax
            'abab5.5-chat',
            // 百川
            'Baichuan4',
            'Baichuan3-Turbo',
            'Baichuan3-Turbo-128k',
            'Baichuan2-Turbo',
            'Baichuan2-Turbo-192k',
        ],
        'vector' => [
            'text-embedding-ada-002',
            'text-embedding-3-small',
            'text-embedding-3-large',
            'zhipu',
            'xunfei',
            'm3e',
            'm3e-small',
            'm3e-base',
            'm3e-large'
        ]
    ]
];
