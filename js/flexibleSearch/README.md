flexibleSearch.js - jQuery plugin
=========================

あらかじめ用意したJSONファイルを検索することにより超高速JavaScript検索を実現するjQueryプラグインです。JSONファイルさえ用意できれば、CMSの種類、スタティックなHTMLサイトなどは問いません。

なお、Movable Type 6 で使う場合は、ダイナミックに Data API にパラメータを渡して検索することもできます。

## 前バージョンとの違い

前バージョンはURLにハッシュを付け、その検索条件をcookieに保存するなど少し特殊な方法でしたが、本バージョンからは通常の検索と同様にURLにパラメータを付けてGETリクエストするような方法で検索します。

したがって、前バージョンではやりにくかった検索結果表示ページを用意する通常の運用が可能です。

## 使い方

### ファイルのアップロードとHTMLの準備

サーバー上のドメインルートに下記のファイルをアップロードします。アップロード先を変更する場合は、適宜置き換えてください。

* /flexibleSearch/flexibleSearch.js
* /flexibleSearch/flexibleSearch-config.js
* /flexibleSearch/mustache.js
* /flexibleSearch/loading.gif

mustache.jsはHTMLを簡単に書き出せるJavaScript用のテンプレートエンジンです。

* [janl/mustache.js](https://github.com/janl/mustache.js)

### HTMLの用意

検索結果を表示するHTMLページを用意し、jQuery、mustache.js、flexibleSearch.js、flexibleSearch-config.jsの順番で読み込みます。

ここでは以下の様に、サイトのトップページとしてindex.htmlを、検索結果表示ページとしてsearch.htmlを用意します。

なお、flexibleSearch-config.jsはflexibleSearchを実行するための設定を書くファイルです。直接scriptタグで書いても構いません。

#### index.html

```
<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>flexibleSearch.js</title>
</head>
<body>
<h1>flexibleSearch.js</h1>
<h2>あらかじめ用意したJSONファイルを検索することにより超高速JavaScript検索を実現するjQueryプラグインです。</h2>
<div id="search"></div>

<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="/flexibleSearch/mustache.js"></script>
<script src="/flexibleSearch/flexibleSearch.js"></script>
<script src="/flexibleSearch/flexibleSearch-config.js"></script>
</body>
</html>
```

#### search.html

```
<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>flexibleSearch.js</title>
</head>
<body>
<h1>検索結果</h1>
<div id="search"></div>
<div id="fs-result"></div>

<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="/flexibleSearch/mustache.js"></script>
<script src="/flexibleSearch/flexibleSearch.js"></script>
<script src="/flexibleSearch/flexibleSearch-config.js"></script>
</body>
</html>
```

### JSONファイルの用意

検索用のJSONファイルを用意します。JSONの書式は以下の通りです。

```
{"items": [
	{"title": "タイトル", "contents": "コンテンツ"},
	{"title": "タイトル", "contents": "コンテンツ"}	
]}
```

ルートのitemsは必須です。その値に、各記事ごとのオブジェクトが入った配列並べるスタイルになります。

なお、Movable Typeで記事とウェブページに関するJSONを書き出すときのテンプレートは以下の様になります。カスタムフィールド等も検索対象にする場合は適宜加えて下さい。

#### Movable TypeのインデックステンプレートでJSONを書き出すテンプレート

```
<mt:Unless name="compress" regex_replace="/^\s*\n/gm","">{"items":[
<mt:entries include_blogs="1" lastn="0">
<mt:setvarBlock name="item{title}"><mt:entryTitle></mt:setvarBlock>
<mt:setvarBlock name="item{url}"><mt:entryPermalink></mt:setvarBlock>
<mt:setvarBlock name="item{body}"><mt:entryBody remove_html="1" regex_replace="/\n|\t| |　/g",""></mt:setvarBlock>
<mt:setvarBlock name="item{more}"><mt:entryMore remove_html="1" regex_replace="/\n|\t| |　/g",""></mt:setvarBlock>
<mt:setvarBlock name="item{tag}">,<mt:entryTags glue=","><mt:tagName regex_replace="/ |　/g","%20"></mt:entryTags>,</mt:setvarBlock>
<mt:setvarBlock name="item{category}"><mt:EntryCategories glue=","><mt:CategoryLabel></mt:EntryCategories></mt:setvarBlock>
<mt:var name="item" to_json="1">,
</mt:entries>
<mt:pages lastn="0">
<mt:setvarBlock name="item{title}"><mt:pageTitle></mt:setvarBlock>
<mt:setvarBlock name="item{url}"><mt:pagePermalink></mt:setvarBlock>
<mt:setvarBlock name="item{body}"><mt:pageBody remove_html="1" regex_replace="/\n|\t| |　/g",""></mt:setvarBlock>
<mt:setvarBlock name="item{more}"><mt:pageMore remove_html="1" regex_replace="/\n|\t| |　",""></mt:setvarBlock>
<mt:setvarBlock name="item{tag}">,<mt:pageTags glue=","><mt:tagName regex_replace="/ |　/g","%20"></mt:pageTags>,</mt:setvarBlock>
<mt:setvarBlock name="item{more}"><mt:PageFolder><mt:FolderLabel></mt:PageFolder></mt:setvarBlock>
<mt:var name="item" to_json="1"><mt:unless __last__>,</mt:unless __last__>
</mt:pages>
]}</mt:Unless>
```

### flexibleSearchの実行

HTML内でflexibleSearch-config.jsにオプション等を記述してflexibleSearchを実行します。flexibleSearchはform要素を包含するdiv等のブロック要素に適用します。

必須のオプションはケースによって異なりますが、searchDataPath、searchFormAction、loadingImgPathは指定しておくと良いでしょう。

```
(function ($) {
    $('#search').flexibleSearch({
        searchDataPath: "/flexibleSearch/search.json",
        searchFormAction: "/flexibleSearch/search.html",
        loadingImgPath: "/flexibleSearch/loading.gif",
        dummy: null
    });
})(jQuery);
```

flexibleSearch-config.jsに書く内容は以下のとおりです。flexibleSearch-config.jsには使用できるオプションがコメントアウトされているので、必要に応じてコメントを外して設定を変更してください。

dummy: null は不要ですが、カンマを入れたり入れなかったり変更するのが面倒な場合は最終行に入れておくと良いかもしれません。

以下で、設定できるオプションを解説します。

## オプション

指定出来るオプションは以下の通りです。詳細は後述します。

| オプション名 | 設定値 | 初期値 | 説明 |
|:--|:--|:--|:--|
| [searchDataPath](#searchDataPath) | String<br>Object | "/flexibleSearch/search.json"  | flexibleSearchで検索対象とするJSONファイルのパスを指定します。文字列で１つ指定する方法と、オブジェクトで複数指定する方法があります。 |
| [searchDataPathPreload](#searchDataPathPreload) | String<br>Array<br>Object | "/flexibleSearch/search.json" | 検索実行ページ以外で、検索対象とするJSONファイルをあらかじめ読み込んでおきキャッシュすることができます。文字列で１つ指定する方法と、配列またはオブジェクトで複数指定する方法があります。 |
| [dataApiDataIds](#dataApiDataIds) | String | null | MTのData APIを利用するDataIdを指定します。複数ある場合はカンマ区切りで指定します。DataIdとは、searchDataPathをオブジェクトで指定した場合のプロパティ名のことを指します。 |
| [dataApiParams](#dataApiParams) | Object | null | Data APIを利用する場合に、検索フォームとは別にエンドポイントに渡すパラメータを設定できます。 |
| [cache](#cache) | Boolean |  true | JSONファイルをキャッシュするかどうかを指定します。 |
| [searchFormCreation](#searchFormCreation) | Boolean | true | 検索フォームをJavaScriptで書き出すかどうかを設定します。ここでfalesを設定すれば、HTMLに書かれたスタティックなフォームを利用することができます。ただし、必須の項目があります。 |
| [searchFormHTML](#searchFormHTML) | String | null | JavaScriptで書き出す検索フォームをHTML文字列で設定する場合に使用します。 |
| [searchFormAction](#searchFormAction) | String | (空文字) | form要素のaction属性を指定します。 |
| [searchFormInputType](#searchFormInputType) | String | "search" | form要素のキーワード入力欄のtype属性を指定します。 |
| [searchFormInputPlaceholder](#searchFormInputPlaceholder) | String | "Search words" | form要素のキーワード入力欄に入れるplaceholderを指定します。 |
| [searchFormSubmitBtnText](#searchFormSubmitBtnText) | String | "Search" | form要素の検索実行ボタンのテキストを指定します。 |
| [advancedFormObj](#advancedFormObj) | String | null | advancedFormObjオプションにオブジェクトを設定することでキーワード入力欄以外のフォーム要素を簡単に作成できます。 |
| [loadingImgPath](#loadingImgPath) | String | "/flexibleSearch/loading.gif" | ローディング画像のパスを指定します。 |
| [loadingImgHTML](#loadingImgHTML) | String | null | ローディング画像を直接HTMLで指定することができます。このオプションを指定した場合はloadingImgPathオプションの設定は無視されます。 |
| [resultBlockId](#resultBlockId) | String | "fs-result" | 検索結果やローディング画像入れるブロック要素のIDを指定します。 |
| [resultMsgTmpl](#resultMsgTmpl) | String | null | 検索結果のメッセージを表示するMustacheテンプレートです。 |
| [resultItemTmpl](#resultItemTmpl) | String | null | 検索結果を表示するMustacheテンプレートです。  |
| [paginateId](#paginateId) | String | "fs-paginate" | 検索結果のページ送りを表示するブロックのIDを指定します。 |
| [paginateTmpl](#paginateTmpl) | String | null | 検索結果が複数ページにわたる場合のページ送りを表示するMustacheテンプレートです。 |
| [paginateCount](#paginateCount) | Number | 10 | 1ページに表示する件数をしていします。この値がlimitパラメータになります。 |
| [submitAction](#submitAction) | Function | function (paramArray) { return paramArray; } | フォームがsubmitされ、ページが遷移する前に呼ばれる関数を設定できます。この関数にはシリアライズされたパラメータの配列paramArrayが渡されます。 |
| [ajaxError](#ajaxError) | Function | function (jqXHR, textStatus, errorThrown) { window.alert(textStatus); } | jQuery.ajaxでエラーが起きたときに呼ばれる関数を設定できます。 |

### <a name="searchDataPath"></a>searchDataPath

flexibleSearchで検索対象とするJSONファイルのパスを指定します。文字列で１つ指定する方法と、オブジェクトで複数指定する方法があります。

**設定例**

```
loadingImgPath: "/flexibleSearch/loading.gif",

または

searchDataPath: {
    static: "/flexibleSearch/search_data.js",
    entries: "/mt/mt-data-api.cgi/v1/sites/1/entries"
},
```

### <a name="searchDataPathPreload"></a>searchDataPathPreload

検索実行ページ以外で、検索対象とするJSONファイルをあらかじめ読み込んでおきキャッシュすることができます。文字列で１つ指定する方法と、配列またはオブジェクトで複数指定する方法があります。

**設定例**

```
loadingImgPath: "/flexibleSearch/loading.gif",

または

searchDataPath: [
    "/flexibleSearch/search_data.js",
    "/mt/mt-data-api.cgi/v1/sites/1/entries"
],

または

searchDataPath: {
    static: "/flexibleSearch/search_data.js",
    entries: "/mt/mt-data-api.cgi/v1/sites/1/entries"
},
```

### <a name="dataApiDataIds"></a>dataApiDataIds

MTのData APIを利用するDataIdを指定します。複数ある場合はカンマ区切りで指定します。DataIdとは、searchDataPathをオブジェクトで指定した場合のプロパティ名のことを指します。 

**設定例**

```
dataApiDataIds: "entries,categories",
```

### <a name="dataApiParams"></a>dataApiParams

Data APIを利用する場合に、検索フォームとは別にエンドポイントに渡すパラメータを設定できます。

**設定例**

```
dataApiParams: {
    fields: "title,keywords",
    searchFields: "title,body,keywords"
},
```

### <a name="cache"></a>cache

**設定例**

```
cache: false,
```

### <a name="searchFormCreation"></a>searchFormCreation

検索フォームをJavaScriptで書き出すかどうかを設定します。ここでfalesを設定すれば、HTMLに書かれたスタティックなフォームを利用することができます。ただし、以下のname値を持つ要素は必須です。

* offset
* limit
* search

**設定例**

```
searchFormCreation: false,
```

### <a name="searchFormHTML"></a>searchFormHTML

JavaScriptで書き出す検索フォームをHTML文字列で設定する場合に使用します。

**設定例**

```
searchFormHTML: ['<form action="/search.html" method="GET">',
  '<input type="hidden" name="offset" value="0">',
  '<input type="hidden" name="limit" value="10">',
  '<input type="text" name="search" value="">',
  '<input type="radio" name="category" value="cat1">',
  '<input type="radio" name="category" value="cat2">',
  '<input type="submit" value="Search">',
'</form>'].join(""),
```

### <a name="searchFormAction"></a>searchFormAction

form要素のaction属性を指定します。

**設定例**

```
searchFormAction: "search.html",
```

### <a name="searchFormInputType"></a>searchFormInputType

form要素のキーワード入力欄のtype属性を指定します。

**設定例**

```
searchFormInputType: "text",
```

### <a name="searchFormInputPlaceholder"></a>searchFormInputPlaceholder

form要素のキーワード入力欄に入れるplaceholderを指定します。

**設定例**

```
searchFormInputPlaceholder: "キーワードを入力",
```

### <a name="searchFormSubmitBtnText"></a>searchFormSubmitBtnText

form要素の検索実行ボタンのテキストを指定します。

**設定例**

```
searchFormSubmitBtnText: "検索",
```

### <a name="advancedFormObj"></a>advancedFormObj

advancedFormObjオプションにオブジェクトを設定することでキーワード入力欄以外のフォーム要素を作成できます。このオプションでは以下の要素を書き出すことができます。

* input:hidden
* input:text
* input:checkbox
* input:radio
* select

基本的な設定方法は以下の書式になります。

```
advancedFormObj: {
    要素タイプ: [
        {属性名: "属性値", 属性名: "属性値" ... },
        ...（いくつでも設定できます）
    ]
}
```

属性値を空にするか、属性名の指定をしないものは、その属性自体が書き出されなくなります。

詳細は下記の個別項目を参照してください。なお、「HTML出力例」は実際には改行なしの1行になります。

#### input:hidden要素

**設定例**

```
advancedFormObj: {
    hidden: [
        {id: "id値", name: "name値", value: "value値"},
        ...（いくつでも設定できます）
    ]
}
```

**HTML出力例**

```
<div class="fs-advanced-hidden" style="display:none;">
    <input type="hidden" id="id値" name="name値" value="value値">
</div>
```

#### input:text要素

**設定例**

```
advancedFormObj: {
    text: [
        {id: "id値", name: "name値", value: "value値", placeholder: "placeholder値", label: "label値"},
        {id: "id値", name: "name値", value: "value値", placeholder: "", label: ""},
        ...（いくつでも設定できます）
    ]
}
```

**HTML出力例**

```
<div class="fs-advanced-text">
    <label id="id値-label" for="name値" class="fs-text fs-name値">
        <input id="id値" type="text" name="name値" value="value値" placeholder="placeholder値">
        label値
    </label>
    <input id="id値" type="text" name="name値" value="value値">
</div>
```

#### input:checkbox要素

**設定例**

```
advancedFormObj: {
    checkbox: [
        {id: "id値", name: "name値", value: "value値", label: "label値"},
        ...（いくつでも設定できます）
    ]
}
```

**HTML出力例**

```
<div class="fs-advanced-checkbox">
    <label id="id値-label" for="name値" class="fs-checkbox fs-name値">
        <input type="checkbox" id="id値" name="name値" value="value値">
        label値
    </label>
</div>
```

#### input:radio要素

**設定例**

```
advancedFormObj: {
    radio: [
        {id: "id値", name: "name値", value: "value値", label: "label値"},
        ...（いくつでも設定できます）
    ]
}
```

**HTML出力例**

```
<div class="fs-advanced-radio">
    <label id="id値-label" for="name値" class="fs-radio fs-name値">
        <input type="radio" id="id値" name="name値" value="value値">
        label値
    </label>
</div>
```

#### select要素

**設定例**

```
advancedFormObj: {
    select: [
        {id: "id値", name: "name値", size: "", multiple: "", option: [
            {label: "選択してください", value: ""},
            {label: "opt_label1", value: "opt_value1"},
            {label: "opt_label2", value: "opt_value2"},
            {label: "opt_label3", value: "opt_value3"}
        ]},
        ...（いくつでも設定できます）
    ]
}
```

**HTML出力例**

```
<div class="fs-advanced-select">
    <select id="id値" for="name値" class="fs-select fs-name値">
        <option value="">選択してください</option>
        <option value="opt_value1">opt_label1</option>
        <option value="opt_value2">opt_label2</option>
        <option value="opt_value3">opt_label3</option>
    </select>
</div>
```

**advancedFormObj全体の設定例**

```
advancedFormObj: {
    hidden: [
        {id: "id値", name: "name値1", value: "value値"}
    ],
    text: [
        {id: "id値1", name: "name値2", value: "value値1", placeholder: "placeholder値1", label: "label値1"},
        {id: "id値2", name: "name値3", value: "value値2", placeholder: "placeholder値2", label: "label値2"}
    ],
    checkbox: [
        {id: "id値1", name: "name値4", value: "value値1", label: "label値1"},
        {id: "id値2", name: "name値5", value: "value値2", label: "label値2"}
    ],
    radio: [
        {id: "id値1", name: "name値6", value: "value値1", label: "label値1"},
        {id: "id値2", name: "name値6", value: "value値2", label: "label値2"}
    ],
    select: [
        {id: "id値1", name: "name値7", size: "", multiple: "", option: [
            {label: "選択してください", value: ""},
            {label: "opt_label1", value: "opt_value1"},
            {label: "opt_label2", value: "opt_value2"},
            {label: "opt_label3", value: "opt_value3"}
        ]},
        {id: "id値2", name: "name値8", size: "3", multiple: "multiple", option: [
            {label: "opt_label1", value: "opt_value1"},
            {label: "opt_label2", value: "opt_value2"},
            {label: "opt_label3", value: "opt_value3"}
        ]}
    ]
},
```

### <a name="loadingImgPath"></a>loadingImgPath

ローディング画像のパスを指定します。

**設定例**

```
loadingImgPath: "/loading.gif",
```

loadingImgPathを指定すると、自動的に次のようなHTMLが検索結果表示ブロックの中に書き出されます。なお、検索結果表示ブロックの中身はappendやprependではなくinnerHTMLでまるごと書き換わります。

```
<span class="fs-loading"></span>
```

### <a name="loadingImgHTML"></a>loadingImgHTML

ローディング画像を直接HTMLで指定することができます。このオプションを指定した場合はloadingImgPathオプションの設定は無視されます。

**設定例**

```
loadingImgHTML: "<img src=\"/loading.gif\" alt=\"読み込み中\">",
```

### <a name="resultBlockId"></a>resultBlockId

検索結果やローディング画像入れるブロック要素のIDを指定します。

**設定例**

```
resultBlockId: "contents-inner",
```

### <a name="resultMsgTmpl"></a>resultMsgTmpl

検索結果の上部に表示するメッセージのMustacheテンプレートです。このオプションを指定しない場合は、次のテンプレートが使用されます。

```
<div id="fs-result-msg">
    <p>
        {{#keywords}}「{{keywords}}」が {{/keywords}}
        {{#count}}{{count}} 件見つかりました。{{/count}}
        {{^count}}見つかりませんでした。{{/count}}
        {{#count}}（{{lastPage}} ページ中 {{currentPage}} ページ目を表示）{{/count}}
    </p>
</div>
```

{{項目名}}の部分は適宜該当する項目に置き換わりますので、resultMsgTmplオプションを指定する場合は、上記と同様に{{項目名}}の各項目を入れてください。

**設定例**

```
resultMsgTmpl: ['<div id="fs-result-msg">',
    '<p>{{#keywords}}「{{keywords}}」が {{/keywords}}{{count}} 件見つかりました。',
    '（{{firstPage}}〜{{lastPage}} ページ中 {{currentPage}} ページ目を表示）</p>',
'</div>'].join(""),
```

テンプレートの書き方は[janl/mustache.js](https://github.com/janl/mustache.js)を参照してください。

### <a name="resultItemTmpl"></a>resultItemTmpl

検索結果を表示するMustacheテンプレートです。このオプションを指定しない場合は、次のテンプレートが使用されます。

```
<div id="fs-result-items">
    <ul>
    {{#items}}
        <li>{{&title}}</li>
    {{/items}}
    </ul>
</div>
```

{{#items}}〜{{/items}}で囲まれている内部が検索結果件数分ループし、その中の{{項目名}}の部分はitemsのプロパティ名を指定します。resultItemHTMLオプションでHTMLを指定する場合は、上記HTMLと同様に{{項目名}}の各項目を入れてください。

テンプレートの書き方は[janl/mustache.js](https://github.com/janl/mustache.js)を参照してください。

**設定例**

```
resultItemTmpl: [
	'<div id="' + op.resultBlockId + '-items">',
    	'<ul>',
    	'{{#items}}',
        	'<li><a href="{{permalink}}">{{&title}}</a></li>',
	    '{{/items}}',
    	'</ul>',
	'</div>'
].join("");,
```

### <a name="paginateId"></a>paginateId

検索結果のページ送りを表示するブロックのIDを指定します。

**設定例**

```
paginateId: "paginate",
```

### <a name="paginateTmpl"></a>paginateTmpl

検索結果が複数ページにわたる場合のページ送りを表示するMustacheテンプレートです。このオプションを指定しない場合は、次のテンプレートが使用されます。

```
<div id="fs-paginate">
    <ul>
        {{#page}}
        <li{{&current}}><span><a href="#" title="{{.}}">{{.}}</a></span></li>
        {{/page}}
    </ul>
</div>
```

{{#page}}〜{{/page}}で囲まれている内部がページ数分ループします。{{&current}}はカレントページの時に`` class="fs-current"``が出力されます。{{.}}がページ番号です。paginateHTMLオプションでHTMLを指定する場合は、上記HTMLと同様に{{項目名}}の各項目を入れてください。

テンプレートの書き方は[janl/mustache.js](https://github.com/janl/mustache.js)を参照してください。

**設定例**

```
paginateTmpl: [
    '<div id="fs-paginate">',
        '<ul>',
            '{{#page}}',
            '<li{{&current}}><a href="#" title="{{.}}">{{.}}</a></li>',
            '{{/page}}',
        '</ul>',
    '</div>'
].join(""),
```

### <a name="paginateCount"></a>paginateCount

1ページに表示する件数をしていします。この値がlimitパラメータになります。

**設定例**

```
paginateCount: 20,
```

### <a name="submitAction"></a>submitAction

フォームがsubmitされ、ページが遷移する前に呼ばれる関数を設定できます。この関数にはシリアライズされたパラメータの配列paramArrayが渡されます。

**設定例**

```
submitAction: function (paramArray) {
    var dataapi = false, l = paramArray.length;
    for (var i = 0; i < l; i++) {
        if (paramArray[i].name === "category" && paramArray[i].value === "movabletype") {
            dataapi = true;
        }
    }
    if (dataapi) {
        for (var i = 0; i < l; i++) {
            if (paramArray[i].name === "dataId") {
                paramArray[i].value = "entries";
            }
        }
    }
    return paramArray;
},
```

### <a name="ajaxError"></a>ajaxError

jQuery.ajaxでエラーが起きたときに呼ばれる関数を設定できます。

**設定例**

```
ajaxError: function (jqXHR, textStatus, errorThrown) {
	window.alert(textStatus);
},
```