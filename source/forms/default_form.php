{
  "attributes": {
    "id": "frm",
    "method": "post",
    "action": "my_form.php"
  },
  "elements": [
    {
      "type": "text",
      "id": "f1",
      "name": "f1",
      "validation": [
        "required",
        "alpha"
      ],
      "label": "Stick your name in here, yo."
    },
    {
      "type": "text",
      "id": "f2",
      "name": "f2",
      "validation": [
        {
          "differ": ["f1", "your first name"]
        },
        {
          "requiredif": ["f1"]
        }
      ],
      "label": "Your last name."
    },
    {
      "type": "password",
      "id": "f3",
      "name": "f3",
      "validation": [
        "required",
        {
          "rangelength": [6, 20]
        }
      ],
      "label": "password"
    },
    {
      "type": "fieldset",
      "legend": "Hi",
      "elements": [
        {
          "type": "text",
          "id": "f4",
          "name": "f4",
          "validation": "required",
          "label": "hi."
        },
        {
          "type": "textarea",
          "id": "f5",
          "name": "f5",
          "label": "Textarea"
        },
        {
          "type": "radiogroup",
          "id": "radiogroup1",
          "label": "Radio Group",
          "name": "radiogroup1",
          "validation": "required",
          "radios": [
            {
              "id": "f6",
              "label": "radio",
              "value": "r1"
            },
            {
              "id": "f7",
              "label": "radio2",
              "value": "r2"
            }
          ]
        }
      ]
    },
    {
      "type": "checkbox",
      "id": "chk3",
      "name": "chk3",
      "value": "chk3",
      "label": "Sign up for newsletter?"
    },
    {
      "type": "checkgroup",
      "name": "chckgroup2[]",
      "label": "News letter topics:",
      "id": "chckgroup2",
      "validation": [
        {
          "selectrange": [2,3]
        },
        {
          "requiredif": ["chk3"]
        }
      ],
      "checkboxes": [
        {
          "id": "g2-1",
          "value": "g2-1",
          "label": "one"
        },
        {
          "id": "g2-2",
          "value": "g2-2",
          "label": "two"
        },
        {
          "id": "g2-3",
          "value": "g2-3",
          "label": "three"
        }
      ]
    },
    {
      "type": "select",
      "id": "slct",
      "name": "slct",
      "label": "My select",
      "options": [
        {
          "label": "yoyo",
          "value": "test"
        },
        {
          "label": "hi",
          "value": "hi"
        },
        {
          "label": "poo",
          "value": "poo"
        }
      ]
    },
    {
      "value": "Submit button!",
      "type": "submit"
    }
  ]
}
